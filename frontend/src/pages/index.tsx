import React from "react";

import { useAuth } from "@/components/auth-provider";

export const Index: React.FC = () => {
  const { user, logout } = useAuth();

  return (
    <div className="flex flex-col h-screen justify-center items-center">
      <div className="text-center">
        <h2 className="text-2xl font-bold mb-4">Welcome, {user?.username}!</h2>
        <p>Email: {user?.email}</p>
        <button
          onClick={logout}
          className="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
        >
          Logout
        </button>
      </div>
    </div>
  );
};
