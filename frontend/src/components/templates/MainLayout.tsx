import React from "react";
import Header from "../organisms/Header";
import Footer from "../organisms/Footer";

interface MainLayoutProps {
  children: React.ReactNode;
}

const MainLayout: React.FC<MainLayoutProps> = ({ children }) => {
  return (
    <div className="container mx-auto px-4">
      <Header />
      {children}
      <Footer />
    </div>
  );
};

export default MainLayout;
