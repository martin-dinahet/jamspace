import React from "react";

import { ExampleComponent } from "@/components/example-component";

export const Index: React.FC = () => {
  return (
    <>
      <div className="w-screen min-h-screen flex justify-center items-center">
        <ExampleComponent />
      </div>
    </>
  );
};
