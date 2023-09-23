require("dotenv").config();
const express = require("express");
const path = require("path");
const cookieParser = require("cookie-parser");
const logger = require("morgan");

const indexRouter = require("./routes/index");
const usersRouter = require("./routes/users");
const myCoursesRouter = require("./routes/myCourses");
const coursesRouter = require("./routes/courses");
const chaptersRouter = require("./routes/chapters");
const lessonsRouter = require("./routes/lessons");
const reviewsRouter = require("./routes/reviews");
const imageCoursesRouter = require("./routes/imageCourses");
const mediaRouter = require("./routes/media");
const refreshTokenRouter = require("./routes/refreshTokens");
const mentorsRouter = require("./routes/mentors");
const webhookRouter = require("./routes/webhook");
const orderPaymentsRouter = require("./routes/orderPayments");

const can = require("./middlewares/permission");
const verifyToken = require("./middlewares/verifyToken");

const app = express();

app.use(logger("dev"));
app.use(express.json({ limit: "50mb" }));
app.use(express.urlencoded({ extended: false, limit: "50mb" }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, "public")));

// app.use(function (req, res, next) {
// 	res.header("Access-Control-Allow-Origin", "http://localhost:3333/"); // update to match the domain you will make the request from
// 	res.header(
// 		"Access-Control-Allow-Headers",
// 		"Origin, X-Requested-With, Content-Type, Accept"
// 	);
// 	next();
// });

app.use("/", indexRouter);
app.use("/users", usersRouter);
app.use("/courses", coursesRouter);
app.use("/mentors", verifyToken, can("admin"), mentorsRouter);
app.use("/lessons", verifyToken, can("admin"), lessonsRouter);
app.use("/chapters", verifyToken, can("admin"), chaptersRouter);
app.use("/image-courses", verifyToken, can("admin"), imageCoursesRouter);
app.use("/my-courses", verifyToken, can("admin", "student"), myCoursesRouter);
app.use("/reviews", verifyToken, can("admin", "student"), reviewsRouter);
app.use("/orders", verifyToken, can("admin", "student"), orderPaymentsRouter);
app.use("/media", verifyToken, can("admin", "student"), mediaRouter);

app.use("/refresh-tokens", refreshTokenRouter);
app.use("/webhook", webhookRouter);

module.exports = app;
