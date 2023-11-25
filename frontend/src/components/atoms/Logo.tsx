import React from "react";

// Substitua com o caminho para o seu arquivo de imagem de logo
import logoImage from "../../assets/logo.png";

interface LogoProps {
  altText?: string; // Texto alternativo para acessibilidade
}

const Logo: React.FC<LogoProps> = ({ altText = "Logo da Empresa" }) => {
  return <img src={logoImage} alt={altText} />;
};

export default Logo;
