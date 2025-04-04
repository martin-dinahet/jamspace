import { User } from "@/lib/types";

export const getAllUsers = async (token: string): Promise<User[]> => {
  const res = await fetch("http://localhost:8000/users", {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
  if (!res.ok) return [];
  return await res.json();
};
