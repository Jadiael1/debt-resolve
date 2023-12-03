interface CardProps {
  children: React.ReactNode;
  title: string;
}

const Card = ({ title, children }: CardProps) => {
  return (
    <div className="w-full md:w-1/3 px-4 mb-10">
      <div className="bg-white rounded-lg shadow p-6 transition duration-300 ease-in-out hover:shadow-lg hover:-translate-y-1">
        <h3 className="text-lg font-medium text-gray-900">{title}</h3>
        <p className="mt-2 text-base text-gray-600">{children}</p>
      </div>
    </div>
  );
};

export default Card;
