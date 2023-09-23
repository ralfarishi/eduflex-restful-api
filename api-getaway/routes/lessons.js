const express = require("express");
const router = express.Router();

const lessonsHandler = require("./handler/lessons");

const verifyToken = require("../middlewares/verifyToken");

router.get("/", verifyToken, lessonsHandler.getAll);
router.get("/:id", verifyToken, lessonsHandler.get);

router.post("/", verifyToken, lessonsHandler.create);
router.put("/:id", verifyToken, lessonsHandler.update);
router.delete("/:id", verifyToken, lessonsHandler.destroy);

module.exports = router;
