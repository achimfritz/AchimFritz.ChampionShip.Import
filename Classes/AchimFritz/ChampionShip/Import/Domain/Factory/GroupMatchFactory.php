<?php
namespace AchimFritz\ChampionShip\Import\Domain\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "AchimFritz.ChampionShip.Import".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use AchimFritz\ChampionShip\Domain\Model\GroupMatch;
use AchimFritz\ChampionShip\Domain\Model\Result;
use AchimFritz\ChampionShip\Domain\Model\GroupRound;
use AchimFritz\ChampionShip\Domain\Model\Cup;
use AchimFritz\ChampionShip\Import\Domain\Model\Match;

/**
 * GroupMatchFactory
 *
 * @Flow\Scope("singleton")
 */
class GroupMatchFactory {

   /**
    * @Flow\Inject
    * @var \AchimFritz\ChampionShip\Domain\Repository\GroupMatchRepository
    */
   protected $groupMatchRepository;

   /**
    * createFromMatch
    * 
    * @param AchimFritz\ChampionShip\Import\Domain\Model\Match $match
    * @param AchimFritz\ChampionShip\Domain\Model\Cup $cup
    * @param array $teams
    * @param AchimFritz\ChampionShip\Domain\Model\GroupRound $groupRound
    * @return GroupMatch $groupMatch
    */
   public function createFromMatch(Match $match, array $teams, Cup $cup, GroupRound $groupRound) {
		$groupMatch = $this->groupMatchRepository->findByTwoTeamsAndCup(
			$teams[$match->getHomeTeam()],
			$teams[$match->getGuestTeam()],
			$cup
		)->getFirst();
		if (!$groupMatch instanceof GroupMatch) {
			$groupMatch = new GroupMatch();
			$this->groupMatchRepository->add($groupMatch);
		}
		$groupMatch->setName($match->getName());
		$groupMatch->setHostTeam($teams[$match->getHomeTeam()]);
		$groupMatch->setGuestTeam($teams[$match->getGuestTeam()]);
		$groupMatch->setCup($cup);
		$startDate = new \DateTime();
		$startDate->setTimestamp($match->getStartDate());
		$groupMatch->setStartDate($startDate);
		$groupMatch->setRound($groupRound);
		if ((int)$match->getHomeGoals() === $match->getHomeGoals() AND (int)$match->getGuestGoals() === $match->getGuestGoals()) {
			$result = new Result();
			$result->setHostTeamGoals((int)$match->getHomeGoals());
			$result->setGuestTeamGoals((int)$match->getGuestGoals());
			$groupMatch->setResult($result);
		}
		$this->groupMatchRepository->update($groupMatch);
		return $groupMatch;

   }

}

?>
