import React from "react";
import Header from "../organisms/Header";
import Footer from "../organisms/Footer";
import Navbar from "../organisms/Navbar";

interface MainLayoutProps {
  children: React.ReactNode;
}

const MainLayout: React.FC<MainLayoutProps> = ({ children }) => {
  return (
    <div className="flex flex-col min-h-screen">
      <Navbar />
      <Header />
      {children}
      <Footer />
    </div>
  );
};

export default MainLayout;
