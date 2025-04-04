import React from "react";

import { useAuth } from "@/lib/auth-context";
import { useNavigate } from "react-router-dom";
import { NavLink } from "react-router-dom";
import { Card } from "@/components/ui/card";
import { CardHeader } from "@/components/ui/card";
import { CardTitle } from "@/components/ui/card";
import { CardDescription } from "@/components/ui/card";
import { CardContent } from "@/components/ui/card";
import { CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

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
    <>
      <div className="flex w-full h-screen justify-center items-center">
        <Card className="w-[20rem]">
          <CardHeader>
            <CardTitle>Login</CardTitle>
            <CardDescription>Please login to your account.</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleLogin} className="flex flex-col gap-3">
              <Input
                type="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
              <Input
                type="password"
                placeholder="Password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
              <Button type="submit" variant="default" className="w-full">
                Login
              </Button>
            </form>
          </CardContent>
          <CardFooter>
            <Button variant="outline" className="w-full" asChild>
              <NavLink to="/register">First time here? Register</NavLink>
            </Button>
          </CardFooter>
        </Card>
      </div>
    </>
  );
};
