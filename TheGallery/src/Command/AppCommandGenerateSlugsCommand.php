<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:generate-slugs')]
class AppCommandGenerateSlugsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $postRepository = $this->entityManager->getRepository(Post::class);
        $posts = $postRepository->findAll();

        foreach ($posts as $post) {
            if (empty($post->getSlug())) { // Si pas encore de slug
                $post->setSlug($this->slugify($post->getTitle())); // Générer un slug
            }
        }

        $this->entityManager->flush(); // Sauvegarde en base

        $output->writeln('Tous les slugs ont été générés avec succès.');
        return Command::SUCCESS;
    }

    private function slugify(string $text): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
}












// namespace App\Command;

// use Symfony\Component\Console\Attribute\AsCommand;
// use Symfony\Component\Console\Command\Command;
// use Symfony\Component\Console\Input\InputArgument;
// use Symfony\Component\Console\Input\InputInterface;
// use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Component\Console\Style\SymfonyStyle;

// #[AsCommand(
//     name: 'AppCommandGenerateSlugsCommand',
//     description: 'Add a short description for your command',
// )]
// class AppCommandGenerateSlugsCommand extends Command
// {
//     public function __construct()
//     {
//         parent::__construct();
//     }

//     protected function configure(): void
//     {
//         $this
//             ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//             ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//         ;
//     }

//     protected function execute(InputInterface $input, OutputInterface $output): int
//     {
//         $io = new SymfonyStyle($input, $output);
//         $arg1 = $input->getArgument('arg1');

//         if ($arg1) {
//             $io->note(sprintf('You passed an argument: %s', $arg1));
//         }

//         if ($input->getOption('option1')) {
//             // ...
//         }

//         $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

//         return Command::SUCCESS;
//     }
// }
