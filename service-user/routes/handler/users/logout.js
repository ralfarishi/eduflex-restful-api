const { User, RefreshToken } = require("../../../models");

module.exports = async (req, res) => {
	const userId = req.body.user_id;
	const user = await User.findByPk(userId);

	if (!user) {
		return res.status(404).json({
			status: "Error",
			message: "User not found",
		});
	}

	// delete refresh token if user is exist
	await RefreshToken.destroy({
		where: { user_id: userId },
	});

	return res.json({
		status: "Success",
		message: "Logout success!",
	});
};
