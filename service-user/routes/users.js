const express = require("express");
const router = express.Router();

const usersHandler = require("./handler/users");

router.post("/register", usersHandler.register);
router.post("/login", usersHandler.login);
router.post("/logout", usersHandler.logout);
router.put("/:id", usersHandler.update);
router.delete("/:id", usersHandler.destroy);
router.get("/:id", usersHandler.getUserById);
router.get("/", usersHandler.getUsers);

module.exports = router;
