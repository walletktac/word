<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Word\Domain\Entity\Word;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class WordFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['seed-words'];
    }

    public function load(ObjectManager $objectManager): void
    {
        $data = [
            ['meticulous', 'adjective', 'very careful and with great attention to detail', ['She keeps meticulous records.'], 'C1', ['quality']],
            ['stumble upon', 'phrasal verb', 'to find something by chance', ['I stumbled upon a great café.'], 'B2', ['phrasal', 'travel']],
            ['resilient', 'adjective', 'able to quickly recover from difficulties', ['Children are often more resilient than adults.'], 'C1', ['character']],
            ['tedious', 'adjective', 'too long, slow, or dull; tiresome', ['Filling out these forms is so tedious.'], 'B2', ['emotion', 'work']],
            ['versatile', 'adjective', 'able to adapt to many different functions or activities', ['She is a versatile actor who plays many roles.'], 'C1', ['skills']],
            ['daunting', 'adjective', 'seeming difficult to deal with in prospect', ['Climbing Mount Everest is a daunting challenge.'], 'C1', ['emotion', 'challenge']],
            ['alleviate', 'verb', 'to make less severe', ['The medicine helped alleviate the pain.'], 'B2', ['health']],
            ['keen on', 'phrase', 'very interested in or enthusiastic about something', ['He is keen on football.'], 'B1', ['interest']],
            ['shortcoming', 'noun', 'a fault or failure to meet a certain standard', ['Her lack of patience is her biggest shortcoming.'], 'C1', ['character']],
            ['inevitable', 'adjective', 'certain to happen; unavoidable', ['Death is inevitable.'], 'B2', ['general']],
            ['thrive', 'verb', 'to grow or develop well or vigorously', ['The business is thriving.'], 'B2', ['business', 'growth']],
            ['scarce', 'adjective', 'in short supply', ['Water is scarce in the desert.'], 'B2', ['environment']],
            ['astonishing', 'adjective', 'extremely surprising or impressive', ['Her memory is astonishing.'], 'B2', ['emotion']],
            ['conceive', 'verb', 'to imagine or form an idea of something', ['It’s hard to conceive how it happened.'], 'C1', ['thinking']],
            ['prosperity', 'noun', 'the state of being successful, usually by making a lot of money', ['The country enjoyed years of prosperity.'], 'B2', ['economy']],
            ['convey', 'verb', 'to express or communicate a message', ['He conveyed his thanks in a letter.'], 'B2', ['communication']],
            ['rigorous', 'adjective', 'extremely thorough and careful', ['The training program is rigorous.'], 'C1', ['quality']],
            ['sustain', 'verb', 'to keep something going over time', ['The pillars sustain the roof.'], 'B2', ['general']],
            ['widespread', 'adjective', 'found or distributed over a large area or number of people', ['The disease was widespread.'], 'B2', ['general']],
            ['notion', 'noun', 'a belief or idea', ['She had a notion that he was lying.'], 'B2', ['thinking']],
            ['notion', 'noun', 'a belief or idea', ['She had a notion that he was lying.'], 'B2', ['thinking']],
        ];
        foreach ($data as [$head,$pos,$def,$ex,$lvl,$tags]) {
            $w = new Word($head);
            $w->setHeadword($head);
            $w->setPos($pos);
            $w->setDefinition($def);
            $w->setExamples($ex);
            $w->setLevel($lvl);
            $w->setTags($tags);
            $objectManager->persist($w);
        }
        echo 'Prepared '.count($data)." rows\n";

        $objectManager->flush();
        echo "Flushed!\n";
    }
}
