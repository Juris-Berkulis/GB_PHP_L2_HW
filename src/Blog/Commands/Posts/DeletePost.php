<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands\Posts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{

    public function __construct(
        // Внедряем репозиторий статей
        private readonly PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct();
    }

    /**
     * Конфигурировать команду
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Удалить статью')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID поста для удаления'
            )
            // Добавили опцию
            ->addOption(
                // Имя опции (написание внутри команды "--check-existence")
                'check-existence',
                // Сокращённое имя (написание внутри команды "-c")
                'c',
                // Опция не имеет значения
                InputOption::VALUE_NONE,
                // Описание
                'Проверить, существует ли статья',
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $question = new ConfirmationQuestion(
            // Вопрос для подтверждения
            'Удалить статью [Y/n]? ',
            // По умолчанию не удалять
            false
        );

        // Ожидаем подтверждения
        if (!$this->getHelper('question')->ask($input, $output, $question)) {
            $output->writeln("Удаление статьи отменено");

            // Выходим, если удаление не подтверждено
            return Command::SUCCESS;
        }

        // Получаем UUID статьи
        $uuid = new UUID($input->getArgument('uuid'));

        // Если опция проверки существования статьи "check-existence" установлена
        if ($input->getOption('check-existence')) {
            try {
                // Пытаемся получить статью
                $this->postsRepository->get($uuid);
            } catch (PostNotFoundException $e) {
                // Выходим, если статья не найдена
                $output->writeln($e->getMessage());

                return Command::FAILURE;
            }
        }

        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);

        $output->writeln("Стаья $uuid удалена");

        return Command::SUCCESS;
    }

}
