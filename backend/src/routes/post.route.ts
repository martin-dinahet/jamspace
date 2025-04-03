import { Router } from "express";
import { Request } from "express";
import { Response } from "express";
import { AppDataSource } from "../data-source";
import { Post } from "../entity/post.entity";
import { User } from "../entity/user.entity";

// initialize the router
const router = Router();
// get the post repository for interacting with the db
const postRepository = AppDataSource.getRepository(Post);
// get the user repository for interacting with the db
const userRepository = AppDataSource.getRepository(User);

// GET ALL POSTS ROUTE
router.get("/", async (req: Request, res: Response) => {
  // get all posts from the db
  const posts = await postRepository.find();
  // respond to the request with all the posts
  res.json(posts);
});

// CREATE NEW POST ROUTE
router.post("/", async (req: Request, res: Response) => {
  // get the post information from the request body
  const { userId, image, description } = req.body;
  // get the user that is going to be the post author
  const user = await userRepository.findOneBy({ id: userId });
  // check if the user actually exists
  if (!user) {
    res.status(404).json({ message: "user not found" });
    return;
  }
  // create the post
  const post = postRepository.create({ user, image, description });
  await postRepository.save(post);
  // respond to the request with the newly created post
  res.status(201).json(post);
});

// UPDATE POST ROUTE
router.put("/:id", async (req: Request, res: Response) => {
  // get the new data from the request body
  const { image, description } = req.body;
  // get the post id from the params
  const { id } = req.params;
  // find the post in the db
  const post = await postRepository.findOne({ where: [{ id }] });
  // check if the post actually exists
  if (!post) {
    res.status(404).json({ message: "post not found" });
    return;
  }
  // replace old data with new
  post.image = image ?? post.image;
  post.description = description ?? post.description;
  // save updated post to the db
  await postRepository.save(post);
  // respond to the request with the updated post
  res.json(post);
});

// DELETE POST ROUTE
router.delete("/:id", async (req: Request, res: Response) => {
  // get the post id from the params
  const { id } = req.params;
  // find the post in the db
  const post = await postRepository.findOne({ where: [{ id }] });
  // check if the post actually exists
  if (!post) {
    res.status(404).json({ message: "post not found" });
    return;
  }
  // remove the post from the db
  await postRepository.remove(post);
  // respond to the request with status 204 (deletion successful)
  res.status(204).send();
});

export default router;
