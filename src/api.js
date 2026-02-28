import axios from "axios";

function getVisitorId() {
  let id = localStorage.getItem("visitor-id");
  if (!id) {
    id = crypto.randomUUID();
    localStorage.setItem("visitor-id", id);
  }
  return id;
}

const api = axios.create({
  baseURL: import.meta.env.VITE_NODE_HOST,
  headers: {
    "Content-type": "application/json",
    "x-visitor-id": getVisitorId(),
  }
})

export default api;