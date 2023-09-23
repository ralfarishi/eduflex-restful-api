const jwt = require("jsonwebtoken");

const apiAdapter = require("../../apiAdapter");
const {
  URL_SERVICE_USER,
  JWT_SECRET,
  JWT_SECRET_REFRESH_TOKEN,
  JWT_ACCESS_TOKEN_EXPIRED,
  JWT_REFRESH_TOKEN_EXPIRED,
} = process.env;

const api = apiAdapter(URL_SERVICE_USER);

module.exports = async (req, res) => {
  try {
    const user = await api.post("/users/login", req.body);
    const data = user.data.data;

    // generate access token
    const token = jwt.sign({ data }, JWT_SECRET, {
      expiresIn: JWT_ACCESS_TOKEN_EXPIRED,
    });
    // generate refresh token
    const refreshToken = jwt.sign({ data }, JWT_SECRET_REFRESH_TOKEN, {
      expiresIn: JWT_REFRESH_TOKEN_EXPIRED,
    });

    // save refreshToken in user db
    await api.post("/refresh_tokens", {
      refresh_token: refreshToken,
      user_id: data.id,
    });

    return res.json({
      status: "Succes",
      data: {
        token,
        refresh_token: refreshToken,
      },
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
