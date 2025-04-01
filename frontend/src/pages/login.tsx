import React from "react";

import { useAuth } from "@/components/auth-provider";
import { useNavigate } from "react-router-dom";
import { NavLink } from "react-router-dom";

export const Login: React.FC = () => {
  const { login } = useAuth();
  const [email, setEmail] = React.useState("");
  const [password, setPassword] = React.useState("");
  const navigate = useNavigate();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    await login(email, password);
    navigate("/");
  };

  return (
    <div className="flex h-screen justify-center items-center">
      <form onSubmit={handleLogin} className="p-6 bg-gray-200 rounded-lg shadow-md">
        <h2 className="text-xl font-bold mb-4">Login</h2>
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="block w-full p-2 mb-2 border rounded"
          required
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          className="block w-full p-2 mb-2 border rounded"
          required
        />
        <button
          type="submit"
          className="w-full p-2 mb-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Login
        </button>
        <NavLink
          to="/register"
          className="block w-full p-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          First time here ? Register
        </NavLink>
      </form>
    </div>
  );
};
