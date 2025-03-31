import React from "react";

import { Outlet } from "react-router-dom";
import { useAuth } from "@/components/auth-provider";
import { Login } from "@/pages/login";

export const MainLayout: React.FC = () => {
  const { token } = useAuth();

  return (
    <>
      <div className="w-full min-h-screen">{token ? <Outlet /> : <Login />}</div>
    </>
  );
};
