const bcrypt = require("bcrypt");
const { User } = require("../../../models");
const Validator = require("fastest-validator");
const v = new Validator();

module.exports = async (req, res) => {
  const schema = {
    email: "email|empty:false",
    password: "string|min:6",
  };

  const validate = v.validate(req.body, schema);

  if (validate.length) {
    return res.status(400).json({
      status: "Error",
      message: validate,
    });
  }

  // validate email
  const user = await User.findOne({
    where: {
      email: req.body.email,
    },
  });

  // if user email doesn't exist
  if (!user) {
    return res.status(409).json({
      status: "Error",
      message: "Credential does not found in our database",
    });
  }

  // validate password
  const isValidPassword = await bcrypt.compare(
    req.body.password,
    user.password
  );

  // if user password doesn't match
  if (!isValidPassword) {
    return res.status(409).json({
      status: "Error",
      message: "Credential does not found in our database",
    });
  }

  // if email and password is match
  return res.json({
    status: "Success",
    data: {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role,
      avatar: user.avatar,
      profession: user.profession,
    },
  });
};
