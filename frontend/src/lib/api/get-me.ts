import { User } from "@/lib/types";

export const getMe = async (token: string): Promise<User | null> => {
  const res = await fetch("http://localhost:8000/users/me", {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
  if (!res.ok) return null;
  return await res.json();
};
