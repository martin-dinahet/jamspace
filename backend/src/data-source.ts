import "reflect-metadata";

import { DataSource } from "typeorm";
import { User } from "./entity/user.entity";
import { Post } from "./entity/post.entity";

export const AppDataSource = new DataSource({
  type: "better-sqlite3",
  database: "database.sqlite",
  entities: [User, Post],
  synchronize: true,
  logging: false,
});
