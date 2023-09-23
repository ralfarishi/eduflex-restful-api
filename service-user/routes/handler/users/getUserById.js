const { User } = require("../../../models");

module.exports = async (req, res) => {
  // get and find user id
  const id = req.params.id;
  const user = await User.findByPk(id, {
    attributes: ["id", "name", "email", "role", "profession", "avatar"],
  });

  // check user
  if (!user) {
    return res.status(404).json({
      status: "Error",
      message: "User not found",
    });
  }

  // if user exist
  return res.json({
    status: "Success",
    data: user,
  });
};
