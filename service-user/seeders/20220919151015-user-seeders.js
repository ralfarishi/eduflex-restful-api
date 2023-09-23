"use strict";

const bcrypt = require("bcrypt");

module.exports = {
	async up(queryInterface, Sequelize) {
		await queryInterface.bulkInsert(
			"users",
			[
				{
					name: "Faris",
					profession: "Backend Dev",
					role: "admin",
					email: "faris@admin.com",
					password: await bcrypt.hash("123456", 10),
					created_at: new Date(),
					updated_at: new Date(),
				},
				// {
				//   name: "Ramiza",
				//   profession: "Backend Dev",
				//   role: "student",
				//   email: "ramiza@user.com",
				//   password: await bcrypt.hash("54321", 10),
				//   created_at: new Date(),
				//   updated_at: new Date(),
				// },
			],
			{}
		);
	},

	async down(queryInterface, Sequelize) {
		await queryInterface.bulkDelete("users", null, {});
	},
};
