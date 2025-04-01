import React from "react";

import { Outlet } from "react-router-dom";
import { useLocation } from "react-router-dom";
import { useAuth } from "@/components/auth-provider";
import { Login } from "@/pages/login";
import { Register } from "@/pages/register";

export const MainLayout: React.FC = () => {
  const { token } = useAuth();
  const location = useLocation();

  if (!token) {
    switch (location.pathname) {
      case "/login":
        return <Login />;
      case "/register":
        return <Register />;
      default:
        return <Login />;
    }
  }

  return (
    <>
      <div className="w-full min-h-screen">
        <Outlet />
      </div>
    </>
  );
};
