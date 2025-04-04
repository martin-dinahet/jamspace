import { Router } from "express";
import { Request } from "express";
import { Response } from "express";
import { AppDataSource } from "../data-source";
import { User } from "../entity/user.entity";
import { authenticateJWT } from "../middleware/auth.middleware";

// initialize the router
const router = Router();
// get the user repository for interacting with the db
const userRepository = AppDataSource.getRepository(User);

// GET ALL USERS ROUTE
router.get("/", async (req: Request, res: Response) => {
  // get all users from the db
  const users = await userRepository.find();
  // respond to the request with all the users
  res.json(users);
});

// FIND USER BY ID ROUTE
router.get("/:id", async (req: Request, res: Response) => {
  // get the user id from the params
  const { id } = req.params;
  // find the user in the db
  const user = await userRepository.findOne({
    where: [{ id }],
    relations: ["posts"],
  });
  // check if the user actually exists
  if (!user) {
    res.status(404).json({ message: "User not found" });
    return;
  }
  // respond to the request with the user
  res.json(user);
});

// UPDATE USER ROUTE (protected)
router.put("/:id", authenticateJWT, async (req: Request, res: Response) => {
  // get the new data from the request body
  const { username, email, password } = req.body;
  // get the user id from the params
  const { id } = req.params;
  // find the user in the db
  const user = await userRepository.findOne({ where: [{ id }] });
  // check if the user actually exists
  if (!user) {
    res.status(404).json({ message: "user not found" });
    return;
  }
  // replace old data with new
  user.username = username ?? user.username;
  user.email = email ?? user.email;
  if (password) {
    user.password = password;
    // hash password
    await user.hashPassword();
  }
  // save updated user to the db
  await userRepository.save(user);
  // respond to the request with the updated user
  res.json(user);
});

// DELETE USER ROUTE
router.delete("/:id", async (req: Request, res: Response) => {
  // the the user id from the request params
  const { id } = req.params;
  // find the user in the db
  const user = await userRepository.findOne({ where: [{ id }] });
  // check if the user actually exists
  if (!user) {
    res.status(404).json({ message: "User not found" });
    return;
  }
  // remove the user in the db
  await userRepository.remove(user);
  // respond to the request with the updated user
  res.status(204).send();
});

export default router;
