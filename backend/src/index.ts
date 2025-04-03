import express from "express";

import userRoute from "./routes/user.route";
import authRoute from "./routes/auth.route";
import postRoute from "./routes/post.route";

import { AppDataSource } from "./data-source";

const app = express();

app.use(express.json());
app.use("/api/posts", postRoute);
app.use("/api/users", userRoute);
app.use("/api/auth", authRoute);

AppDataSource.initialize()
  .then(() => {
    app.listen(3000, () => {
      console.log("server running at http://localhost:3000");
    });
  })
  .catch((err) => {
    console.error(`error during data source initialization: ${err}`);
  });
