<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class TravisCiQuestionHelper
{
    /** @var  QuestionHelper */
    private $questionHelper;

    /**
     * TravisCiQuestionHelper constructor.
     * @param QuestionHelper $questionHelper
     */
    public function __construct(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $file
     * @return bool
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function confirmOverwriteFile(InputInterface $input, OutputInterface $output, $file)
    {
        $question = new ConfirmationQuestion(sprintf(
            '%s already exists, do you want to overwrite it? [yes]: ',
            $file
        ));

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string[] $allowedVersions
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function askWhichPhpVersionToBuild(
        InputInterface $input,
        OutputInterface $output,
        array $allowedVersions
    ) {
        $question = new Question(sprintf(
            'On which version of PHP do you wish to build? [%s]: ',
            implode('|', $allowedVersions)
        ));

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string[] $allowedVersions
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function askWhichBuildIsAllowedToFail(
        InputInterface $input,
        OutputInterface $output,
        array $allowedVersions
    ) {
        $question = new Question(sprintf(
            'Which version of PHP is allowed to fail? [%s]: ',
            implode('|', $allowedVersions)
        ));

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function confirmComposerSelfUpdate(InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Update Composer to the latest version? [yes]: ');

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function confirmComposerInstall(InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Install dependencies via Composer? [yes]: ');

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function confirmRunPhpUnit(InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Run phpunit? [yes]: ');

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function confirmRunBehat(InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Run behat? [yes]: ');

        return $this->questionHelper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string[] $allowedVersions
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function askWhichPhpVersionsToBuild(
        InputInterface $input,
        OutputInterface $output,
        array $allowedVersions
    ) {
        $versions = [];

        do {
            $version = $this->askWhichPhpVersionToBuild(
                $input,
                $output,
                $allowedVersions
            );

            // Filter by white-listed values.
            if (in_array($version, $allowedVersions, false)) {
                $versions[] = $version;
            }
        } while ($version || count($versions) < 1);

        return $versions;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string[] $allowedVersions
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function askWhichBuildsAreAllowedToFail(
        InputInterface $input,
        OutputInterface $output,
        array $allowedVersions
    ) {
        $versions = [];

        do {
            $version = $this->askWhichBuildIsAllowedToFail(
                $input,
                $output,
                $allowedVersions
            );

            // Filter by white-listed values.
            $versionKey = array_search($version, $allowedVersions, true);
            if ($versionKey !== false) {
                $versions[] = $version;
                unset($allowedVersions[$versionKey]);
            }
        } while ($version && $allowedVersions);

        return $versions;
    }
}
