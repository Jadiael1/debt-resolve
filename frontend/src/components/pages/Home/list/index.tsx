import MainLayout from '../../../templates/MainLayout';
import Card from '../../../atoms/Card';

const HomePage = () => {
	return (
		<MainLayout>
			<section className='py-12 bg-white flex-grow container mx-auto'>
				<div className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'>
					<div className='text-center'>
						<h2 className='text-2xl leading-8 font-semibold text-gray-900'>Funcionalidades</h2>
						<p className='mt-4 max-w-2xl text-xl text-gray-500 mx-auto'>
							Tudo o que você precisa para gerenciar suas cobranças e pagamentos de forma eficiente.
						</p>
					</div>
					<div className='mt-10'>
						<div className='flex flex-wrap -mx-4'>
							<Card title='Crie Cobranças'>
								Registre novas cobranças, defina valores e parcelas, e informe suas informações bancárias para receber
								pagamentos.
							</Card>
							<Card title='Convide Devedores'>
								Envie convites para devedores, seja através de notificações no sistema ou por e-mail para novos
								usuários.
							</Card>
							<Card title='Gerencie Pagamentos'>
								Receba e confirme pagamentos com facilidade, gerenciando o status de cada parcela.
							</Card>
						</div>
					</div>
				</div>
			</section>
		</MainLayout>
	);
};

export default HomePage;
