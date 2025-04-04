import React from "react";

import { useAuth } from "@/lib/auth-context";
import { getAllUsers } from "@/lib/api/get-all-users";
import { User } from "@/lib/types";
import { Post } from "@/lib/types";

import { Avatar } from "@/components/ui/avatar";
import { AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { CardHeader } from "@/components/ui/card";
import { CardTitle } from "@/components/ui/card";
import { CardDescription } from "@/components/ui/card";
import { CardContent } from "@/components/ui/card";
import { CardFooter } from "@/components/ui/card";
import { Heart, Send } from "lucide-react";

export const Index: React.FC = () => {
  const { token, user, logout } = useAuth();
  const [users, setUsers] = React.useState<Array<User>>([]);
  const [posts, setPosts] = React.useState<Array<Post>>([]);

  React.useEffect(() => {
    if (token) {
      getAllUsers(token).then((fetchedUsers) => {
        setUsers(fetchedUsers);
        setPosts(fetchedUsers.flatMap((user) => JSON.parse(user.posts)));
      });
    }
  }, [token]);

  return (
    <>
      <div className="w-screen min-h-screen">
        <header className="sticky top-0 bg-background flex items-center justify-between p-4 border">
          <div className="flex gap-4 items-center">
            <Avatar>
              <AvatarFallback>
                {(user?.username[0]! + user?.username[1]!).toString()}
              </AvatarFallback>
            </Avatar>
            <h1 className="text-lg">
              Welcome back, <span className="font-semibold">{user?.username}</span>!
            </h1>
          </div>
          <Button onClick={() => logout()}>Log out</Button>
        </header>
        <main className="p-4">
          <Card>
            <CardHeader>
              <CardTitle>New Posts</CardTitle>
              <CardDescription>Explore today's new posts</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              {posts.map((post) => (
                <Card key={post.id}>
                  <CardHeader>
                    <CardTitle>
                      <h2>{post.title}</h2>
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-4">
                    <img src={post.image} className="max-h-[10rem] p-x-4 w-full rounded-md" />
                    <div className="flex gap-2 justify-end">
                      <Button variant="secondary">
                        <Heart />
                      </Button>
                      <Button>
                        <Send />
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </CardContent>
          </Card>
        </main>
        <footer></footer>
      </div>
    </>
  );
};
