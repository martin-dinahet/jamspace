import React from "react";

export const Index: React.FC = () => {
  const [users, setUsers] = React.useState<object>({});

  React.useEffect(() => {
    fetch("http://localhost:8000/users/")
      .then((response) => response.json())
      .then((data) => setUsers(data))
      .catch((error) => console.error(error.message));
  }, []);

  return (
    <>
      <main className="flex h-screen justify-center items-center">{JSON.stringify(users)}</main>
    </>
  );
};
