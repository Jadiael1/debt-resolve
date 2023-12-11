import Navbar from '../../organisms/Navbar';
import {
	FaUserTie,
	FaChartLine,
	FaShieldAlt,
	FaUserAstronaut,
	FaUserNinja,
	FaUserSecret,
	FaEnvelope,
	FaUser,
	FaPen,
} from 'react-icons/fa';
const AboutPage = () => {
	return (
		<div className='bg-gray-300'>
			<Navbar />
			<div className='mx-auto px-4 py-8'>
				{/* Seção de Introdução */}
				<section className='text-center py-8'>
					<h1 className='text-4xl font-bold text-gray-800 mb-4'>Sobre o DebtsCRM</h1>
					<p className='text-lg text-gray-600'>
						Gerenciamento eficiente de dívidas e cobranças para empresas e indivíduos.
					</p>
				</section>

				{/* Seção de Recursos */}
				<section className='grid md:grid-cols-3 gap-8 text-center py-8'>
					<div>
						<FaUserTie className='mx-auto text-6xl text-blue-500' />
						<h2 className='text-xl font-semibold my-2'>Atendimento ao Cliente</h2>
						<p className='text-gray-600'>Fornecemos suporte dedicado para ajudar você em todas as etapas.</p>
					</div>
					<div>
						<FaChartLine className='mx-auto text-6xl text-green-500' />
						<h2 className='text-xl font-semibold my-2'>Relatórios Detalhados</h2>
						<p className='text-gray-600'>Acompanhe sua performance e melhore sua estratégia de cobranças.</p>
					</div>
					<div>
						<FaShieldAlt className='mx-auto text-6xl text-red-500' />
						<h2 className='text-xl font-semibold my-2'>Segurança Garantida</h2>
						<p className='text-gray-600'>Seus dados estão seguros conosco, com a mais alta segurança digital.</p>
					</div>
				</section>

				{/* Seção de Contato ou Equipe */}
				<section className='bg-gray-100 py-8 px-8 sm:px-0 rounded mb-2'>
					<div className='text-center'>
						<h2 className='text-3xl font-semibold text-gray-800'>Nossa Equipe</h2>
						<p className='text-lg text-gray-600 mt-2 mb-8'>Conheça os profissionais por trás do DebtsCRM.</p>

						<div className='grid md:grid-cols-3 gap-8 px-4'>
							<div className='text-center'>
								<FaUserAstronaut className='mx-auto text-6xl text-blue-500 mb-4' />
								<h3 className='text-xl font-semibold'>Jadiael Juvino</h3>
								<p className='text-gray-600'>CEO & Fundador</p>
							</div>

							<div className='text-center'>
								<FaUserNinja className='mx-auto text-6xl text-green-500 mb-4' />
								<h3 className='text-xl font-semibold'>Jadiael Juvino</h3>
								<p className='text-gray-600'>Chefe de Desenvolvimento</p>
							</div>

							<div className='text-center'>
								<FaUserSecret className='mx-auto text-6xl text-red-500 mb-4' />
								<h3 className='text-xl font-semibold'>Jadiael Juvino</h3>
								<p className='text-gray-600'>Especialista em Segurança</p>
							</div>
						</div>
					</div>
				</section>
				{/* Seção de Formulário de Contato */}
				<section className='bg-white py-8 rounded'>
					<div className='container mx-auto px-4'>
						<h2 className='text-3xl font-semibold text-center text-gray-800'>Fale Conosco</h2>
						<p className='text-lg text-center text-gray-600 my-4'>Tem perguntas? Gostaríamos de ouvir de você.</p>
						<div className='max-w-lg mx-auto'>
							<form>
								<div className='mb-4'>
									<label
										htmlFor='name'
										className='block text-gray-700 text-sm font-bold mb-2'
									>
										Nome
									</label>
									<div className='flex align-items-center border border-gray-300 rounded px-3 py-2'>
										<FaUser className='text-gray-500 mr-2' />
										<input
											type='text'
											id='name'
											placeholder='Seu nome'
											className='w-full focus:outline-none text-gray-700'
										/>
									</div>
								</div>
								<div className='mb-4'>
									<label
										htmlFor='email'
										className='block text-gray-700 text-sm font-bold mb-2'
									>
										Email
									</label>
									<div className='flex align-items-center border border-gray-300 rounded px-3 py-2'>
										<FaEnvelope className='text-gray-500 mr-2' />
										<input
											type='email'
											id='email'
											placeholder='Seu email'
											className='w-full focus:outline-none text-gray-700'
										/>
									</div>
								</div>
								<div className='mb-4'>
									<label
										htmlFor='message'
										className='block text-gray-700 text-sm font-bold mb-2'
									>
										Mensagem
									</label>
									<div className='flex align-items-center border border-gray-300 rounded px-3 py-2'>
										<FaPen className='text-gray-500 mr-2' />
										<textarea
											id='message'
											rows={4}
											placeholder='Sua mensagem'
											className='w-full focus:outline-none text-gray-700'
										></textarea>
									</div>
								</div>
								<button
									type='submit'
									className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
								>
									Enviar
								</button>
							</form>
						</div>
					</div>
				</section>
			</div>
			<footer className='bg-gray-700'>
				<div className='container mx-auto py-4 text-center text-white'>
					<p>&copy; 2023 DebtsCRM. Todos os direitos reservados.</p>
				</div>
			</footer>
		</div>
	);
};

export default AboutPage;
