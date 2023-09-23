const { User } = require("../../../models");

module.exports = async (req, res) => {
	// get and find user id
	const id = req.params.id;
	const user = await User.findByPk(id);

	// check user
	if (!user) {
		return res.status(404).json({
			status: "Error",
			message: "User not found",
		});
	}

	await user.destroy();

	// if user exist
	return res.json({
		status: "Success",
		message: "User has been deleted!",
	});
};
