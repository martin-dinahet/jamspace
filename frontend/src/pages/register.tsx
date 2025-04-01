import React from "react";

import { useAuth } from "@/components/auth-provider";
import { useNavigate } from "react-router-dom";
import { NavLink } from "react-router-dom";

export const Register: React.FC = () => {
  const { register } = useAuth();
  const [username, setUsername] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [password, setPassword] = React.useState("");
  const navigate = useNavigate();

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    await register(username, email, password);
    navigate("/");
  };

  return (
    <div className="flex h-screen justify-center items-center">
      <form onSubmit={handleRegister} className="p-6 bg-gray-200 rounded-lg shadow-md">
        <h2 className="text-xl font-bold mb-4">Register</h2>
        <input
          type="text"
          placeholder="Username"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          className="block w-full p-2 mb-2 border rounded"
          required
        />
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
          Register
        </button>

        <NavLink
          to="/register"
          className="block w-full p-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Already have an account ? Login
        </NavLink>
      </form>
    </div>
  );
};
