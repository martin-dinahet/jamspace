import * as jwt from "jsonwebtoken";

import { Router } from "express";
import { Request } from "express";
import { Response } from "express";
import { AppDataSource } from "../data-source";
import { User } from "../entity/user.entity";

// create a new express router
const router = Router();
// get the user repository for interacting with the db
const userRepository = AppDataSource.getRepository(User);
// initialize the jwt secret
const JWT_SECRET = "secret";

// REGISTER ROUTE
router.post("/register", async (req: Request, res: Response) => {
  // get the credentials from the request body
  const { username, email, password } = req.body;
  // check if a user exists with the same credentials
  const existingUser = await userRepository.findOne({
    where: [{ username }, { email }],
  });
  if (existingUser) {
    res.status(400).json({ message: "username or email already exists" });
  }
  // create a new user
  const user = new User();
  user.username = username;
  user.email = email;
  user.password = password;
  // save the new user in the db
  await userRepository.save(user);
  // respond to the request with the newly created user
  res.status(201).json(user);
  return;
});

// LOGIN ROUTE
router.post("/login", async (req: Request, res: Response) => {
  // get the credentials from the request body
  const { username, password } = req.body;
  // find the user in the db
  const user = await userRepository.findOne({
    where: [{ username }],
  });
  // check that the user actually exists
  if (!user) {
    res.status(401).json({ message: "invalid username" });
    return;
  }
  // check if password is valid
  const isPasswordValid = await user.validatePassword(password);
  // return error if username or password is invalid
  if (!isPasswordValid) {
    res.status(401).json({ message: "invalid password" });
  }
  // make a new jwt token
  const token = jwt.sign(
    { id: user?.id, username: user?.username },
    JWT_SECRET,
    { expiresIn: "1h" }
  );
  // respond to the request with the token
  res.json({ token });
});

export default router;
