const register = require("./register");
const login = require("./login");
const update = require("./update");
const getUserById = require("./getUserById");
const getUsers = require("./getUsers");
const logout = require("./logout");
const destroy = require("./destroy");

module.exports = {
	register,
	login,
	update,
	getUserById,
	getUsers,
	logout,
	destroy,
};
