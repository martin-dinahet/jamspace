import jwt from "jsonwebtoken";
import { Request, Response, NextFunction } from "express";

// Type guard to check if the decoded JWT matches the expected structure
function isUserPayload(
  decoded: any
): decoded is { id: string; username: string } {
  return (
    decoded &&
    typeof decoded === "object" &&
    "id" in decoded &&
    "username" in decoded
  );
}

export const authenticateJWT = (
  req: Request,
  res: Response,
  next: NextFunction
): void => {
  const token = req.headers["authorization"]?.split(" ")[1];

  if (!token) {
    res.status(401).json({ message: "Access denied. No token provided." });
    return;
  }

  jwt.verify(token, "secret", (err, decoded) => {
    if (err) {
      return res.status(403).json({ message: "Invalid or expired token." });
    }

    // Use the type guard to check if decoded is the expected structure
    if (isUserPayload(decoded)) {
      req.user = decoded; // Now TypeScript knows this is the correct type
      next(); // Proceed to the next middleware or route handler
    } else {
      return res.status(403).json({ message: "Invalid token format." });
    }
  });
};
