import { User } from "@/lib/types";

export const getUserById = async (id: string, token: string): Promise<User | null> => {
  const res = await fetch(`http://localhost:8000/users/${id}`, {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
  if (!res.ok) return null;
  return await res.json();
};
