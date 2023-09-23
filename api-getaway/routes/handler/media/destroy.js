const apiAdapter = require("../../apiAdapter");
const { URL_SERVICE_MEDIA } = process.env;

const api = apiAdapter(URL_SERVICE_MEDIA);

module.exports = async (req, res) => {
  try {
    const id = req.params.id;

    const media = await api.delete(`/media/${id}`);

    return res.json(media.data);
  } catch (err) {
    // check service-media connection
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
