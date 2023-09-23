const { User } = require("../../../models");

module.exports = async (req, res) => {
  // define query params for user id/s
  const userIds = req.query.user_ids || [];

  const sqlOptions = {
    attributes: ["id", "name", "email", "role", "profession", "avatar"],
  };

  // filtering based on the selected id
  if (userIds.length) {
    sqlOptions.where = {
      id: userIds,
    };
  }

  // get all user
  const users = await User.findAll(sqlOptions);

  return res.json({
    status: "Success",
    data: users,
  });
};
