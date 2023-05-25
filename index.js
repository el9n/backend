const fs = require("fs");

const cors = require("cors");
const express = require("express");
const app = express();
const socketio = require("socket.io");

const PORT = 3000;
const HOST = "localhost";

// настройка CORS политики
app.use(cors());
app.use(express.json());

// запуск сервера
const server = app.listen(PORT, () => {
  console.log(`app listening at ${HOST}:${PORT}`);
});
const io = socketio(server, { cors: { origin: "*" } });

// ---------

const CURRENT_GAME_PATH = "/private/current_game.json";
const GAMES_PATH = "/private/games";

const currentGame = JSON.parse(fs.readFileSync(__dirname + CURRENT_GAME_PATH));
const game = JSON.parse(
  fs.readFileSync(__dirname + `${GAMES_PATH}/${currentGame.name}.json`)
);

const MAIN_ROOM_NAME = "main";
const PENDING_ROOM_NAME = "pending";
const USER_NAMES = Object.keys(game.options.players);

const mainNamespace = io.of("/").adapter;

// socket.io соединения
io.on("connection", (user) => {
  console.log("new client connected!");

  // поместить нового пользователя в комнату ожидания
  user.join(PENDING_ROOM_NAME);
});

// присоединение к комнате ожидания
onRoomEvent(PENDING_ROOM_NAME, "join", async (room, id) => {
  console.log(`${id} has joined the ${room} room`);
  const user = getUserById(id);

  const players = await getMainRoomUsers();

  const availableNames = [...USER_NAMES];

  // убрать занятые имена из пула доступных имен
  players.forEach((player) => {
    const index = availableNames.indexOf(player.name);
    if (index > -1) {
      availableNames.splice(index, 1);
    }
  });

  // отправить пользователю список свободных имен
  user.emit("users", availableNames);

  // переместить нового пользователя в главную комнату
  user.name = availableNames[0];
  user.leave(PENDING_ROOM_NAME);
  user.join(MAIN_ROOM_NAME);
});

// присоединение к главной комнате
onRoomEvent(MAIN_ROOM_NAME, "join", async (room, id) => {
  const user = getUserById(id);
  console.log(`${user.name} has joined the ${room} room`);

  getUserById(id).emit("game", game);

  // получить всех пользователей комнаты
  const players = await getMainRoomUsers();
  console.log(
    `${players.length} user(s) in the ${room} room: ${players.map(
      (player) => player.name
    )}`
  );
});

function onRoomEvent(
  roomName,
  eventName,
  callback,
  namespace = io.of("/").adapter
) {
  namespace.on(eventName + "-room", (room, id) => {
    if (room !== roomName) return;

    callback(room, id);
  });
}

async function getMainRoomUsers() {
  return await io.in(MAIN_ROOM_NAME).fetchSockets();
}

function getUserById(id) {
  return io.sockets.sockets.get(id);
}
