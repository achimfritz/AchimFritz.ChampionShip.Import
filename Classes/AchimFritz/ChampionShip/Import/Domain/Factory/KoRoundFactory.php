<?php
namespace AchimFritz\ChampionShip\Import\Domain\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "AchimFritz.ChampionShip.Import".*
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use AchimFritz\ChampionShip\Competition\Domain\Model\ChildKoRound;
use AchimFritz\ChampionShip\Competition\Domain\Model\KoRound;
use AchimFritz\ChampionShip\Competition\Domain\Model\KoMatch;
use AchimFritz\ChampionShip\Competition\Domain\Model\Cup;
use AchimFritz\ChampionShip\Import\Domain\Model\Match;

/**
 * KoRoundFactory
 *
 * @Flow\Scope("singleton")
 */
class KoRoundFactory {

   /**
    * @Flow\Inject
    * @var \AchimFritz\ChampionShip\Competition\Domain\Repository\KoRoundRepository
    */
   protected $koRoundRepository;

   /**
    * createFromMatch
    * 
    * @param AchimFritz\ChampionShip\Import\Domain\Model\Match $match 
	 * @param array $teams
    * @param AchimFritz\ChampionShip\Competition\Domain\Model\Cup $cup
    * @return KoRound $groupRound
    */
   public function createFromMatch(Match $match, array $teams, Cup $cup) {
		$roundType = $match->getRoundType();
		$parent = '';
		switch ($roundType) {
			case 2:
				$name = 'Achtelfinale';
				break;
			case 3:
				$name = 'Viertelfinale';
				$parent = 'Achtelfinale';
				break;
			case 4:
				$name = 'Halbfinale';
				$parent = 'Viertelfinale';
				break;
			case 5:
				$name = 'Finale';
				$parent = 'Halbfinale';
				break;
			case 6:
				$name = 'Verlierer Finale';
				$parent = 'Halbfinale';
				break;
			default:
				throw new \Exception('no such rountType ' . $roundType, 1389721252);
				break;
		}
      $koRound = $this->koRoundRepository->findOneByNameAndCup($name, $cup);
      if (!$koRound instanceof KoRound) {
			if ($match->getRoundType() == 2 AND count($cup->getTeams()) == 32) {
				// wm achtelfinale
				$koRound = new KoRound();
			} elseif ($match->getRoundType() == 3 AND count($cup->getTeams()) == 16) {
				// em viertelfinale
				$koRound = new KoRound();
			} else {
				$parentRound = $this->koRoundRepository->findOneByNameAndCup($parent, $cup);
				if (!$parentRound instanceof koRound) {
					throw new \Exception('no parent round found with name ' . $parent, 1389721253);
				}
				$koRound = new ChildKoRound();
				$koRound->setParentRound($parentRound);
			}
         $this->koRoundRepository->add($koRound);
      }
		$koRound->setCup($cup);
		$koRound->setName($name);
      foreach ($teams AS $team) {
         if (!$koRound->hasTeam($team)) {
            $koRound->addTeam($team);
         }
      }
      $this->koRoundRepository->update($koRound);
      return $koRound;
   }

}

?>
