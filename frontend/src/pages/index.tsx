import React from "react";

export const Index: React.FC = () => {
  const [message, setMessage] = React.useState<string>("");

  React.useEffect(() => {
    fetch("http://localhost:8000/hello")
      .then((response) => response.json())
      .then((data) => setMessage(data.message))
      .catch((error) => setMessage(`Error fetching data: ${error.message}`));
  }, []);

  return (
    <>
      <main className="flex h-screen justify-center items-center">{message || "Loading..."}</main>
    </>
  );
};
