const express = require("express");
const router = express.Router();
const isBase64 = require("is-base64");
const base64Img = require("base64-img");
const fs = require("fs");

// model
const { Media } = require("../models");

const { HOSTNAME } = process.env;

// ! show all image
router.get("/", async (req, res) => {
	const media = await Media.findAll({
		attributes: ["id", "image"],
	});

	const mappedMedia = media.map((m) => {
		m.image = `${HOSTNAME}/${m.image}`;

		return m;
	});

	return res.json({
		status: "Success",
		data: mappedMedia,
	});
});

// ! upload image
router.post("/", (req, res) => {
	const image = req.body.image;

	if (!isBase64(image, { mimeRequired: true })) {
		return res.status(400).json({
			status: "error",
			message: "Invalid Base64",
		});
	}

	base64Img.img(image, "./public/images", Date.now(), async (err, filepath) => {
		if (err) {
			return res.status(400).json({
				status: "Error",
				message: err.massage,
			});
		}

		const filename = filepath.split("\\").pop().split("/").pop();

		// save image to database
		const media = await Media.create({
			image: `images/${filename}`,
		});

		return res.json({
			status: "Success",
			data: {
				id: media.id,
				image: `${HOSTNAME}/images/${filename}`,
			},
		});
	});
});

// ! delete image
router.delete("/:id", async (req, res) => {
	const id = req.params.id;

	const media = await Media.findByPk(id);

	if (!media) {
		return res.status(404).json({
			status: "Error",
			message: "Image not found",
		});
	}

	fs.unlink(`./public/${media.image}`, async (err) => {
		if (err) {
			return res.status(400).json({
				status: "Error",
				message: err.message,
			});
		}

		await media.destroy();

		return res.json({
			status: "Success",
			message: "Image deleted",
		});
	});
});

module.exports = router;
