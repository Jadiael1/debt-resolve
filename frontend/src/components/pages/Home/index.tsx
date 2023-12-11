import Navbar from '../../organisms/Navbar';

const HomePage = () => {
	return (
		<div className='flex flex-col min-h-screen bg-gray-300'>
			<Navbar />

			{/* Hero Section */}
			<header className='bg-gray-200 py-12'>
				<div className='container mx-auto text-center'>
					<h1 className='text-4xl font-bold text-gray-800 mb-4'>Gerencie Cobranças com Facilidade</h1>
					<p className='text-md text-gray-500'>
						Uma solução completa para criar, gerenciar e acompanhar suas cobranças e pagamentos.
					</p>
				</div>
			</header>

			{/* Features Section */}
			<section className='container mx-auto py-12'>
				<div className='text-center mb-12'>
					<h2 className='text-3xl font-semibold text-gray-800'>Principais Funcionalidades</h2>
					<p className='mt-4 text-gray-600'>Descubra como nosso sistema pode facilitar a gestão das suas finanças.</p>
				</div>
				<div className='grid md:grid-cols-3 gap-8 px-6'>
					<div className='feature-card'>
						<h3 className='text-xl font-semibold mb-2'>Criação de Cobranças</h3>
						<p className='text-gray-600'>Defina valores, parcelas e datas de pagamento de forma simples e rápida.</p>
					</div>

					<div className='feature-card'>
						<h3 className='text-xl font-semibold mb-2'>Convites para Devedores</h3>
						<p className='text-gray-600'>
							Convide devedores facilmente, mesmo que eles não estejam registrados no sistema.
						</p>
					</div>

					<div className='feature-card'>
						<h3 className='text-xl font-semibold mb-2'>Gerenciamento de Pagamentos</h3>
						<p className='text-gray-600'>Acompanhe e confirme pagamentos, mantendo tudo organizado e sob controle.</p>
					</div>
				</div>
			</section>

			<div className='bg-blue-600 flex-grow flex items-center'>
				<div className='container mx-auto text-center py-6'>
					<h2 className='text-3xl font-semibold text-white'>Pronto para Começar?</h2>
					<p className='text-white mt-2'>Junte-se a nós e gerencie suas cobranças de maneira eficiente.</p>
					<a
						href='/signup'
						className='mt-4 inline-block bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition duration-300'
					>
						Cadastre-se Agora
					</a>
				</div>
			</div>

			<footer className='bg-gray-700'>
				<div className='container mx-auto py-4 text-center text-white'>
					<p>&copy; 2023 DebtsCRM. Todos os direitos reservados.</p>
				</div>
			</footer>
		</div>
	);
};

export default HomePage;
