const jwt = require("jsonwebtoken");
const apiAdapter = require("../../apiAdapter");
const {
  URL_SERVICE_USER,
  JWT_SECRET,
  JWT_SECRET_REFRESH_TOKEN,
  JWT_ACCESS_TOKEN_EXPIRED,
} = process.env;

const api = apiAdapter(URL_SERVICE_USER);

module.exports = async (req, res) => {
  try {
    // get refresh token from body
    const refreshToken = req.body.refresh_token;
    // get email from body
    const email = req.body.email;

    if (!refreshToken || !email) {
      return res.status(400).json({
        status: "Error",
        message: "Invalid token",
      });
    }

    // check if refresh token is exist in DB using service user API
    await api.get("/refresh_tokens", {
      params: { refresh_token: refreshToken },
    });

    // verify refresh token
    jwt.verify(refreshToken, JWT_SECRET_REFRESH_TOKEN, (err, decoded) => {
      if (err) {
        return res.status(403).json({
          status: "Error",
          message: err.message,
        });
      }

      // verify email
      if (email !== decoded.data.email) {
        return res.status(400).json({
          status: "Error",
          message: "Invalid email",
        });
      }

      // generate new access token
      const token = jwt.sign({ data: decoded.data }, JWT_SECRET, {
        expiresIn: JWT_ACCESS_TOKEN_EXPIRED,
      });

      return res.json({
        status: "Success",
        data: {
          token,
        },
      });
    });
  } catch (err) {
    // check service-user connection
    if (err.code === "ECONNREFUSED") {
      return res.status(500).json({
        status: "Error",
        message: "Service unavailable",
      });
    }

    const { status, data } = err.response;
    return res.status(status).json(data);
  }
};
