// src/api/users/updateUser.ts
import { User } from "@/lib/types";

export const updateUser = async (
  id: string,
  token: string,
  data: Partial<User>,
): Promise<User | null> => {
  const res = await fetch(`http://localhost:8000/users/${id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(data),
  });
  if (!res.ok) return null;
  return await res.json();
};
