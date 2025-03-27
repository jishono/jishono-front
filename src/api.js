import axios from "axios";

const api = axios.create({
  baseURL: import.meta.env.VITE_NODE_HOST,
  headers: {
    "Content-type": "application/json",
  }
})

export default api;