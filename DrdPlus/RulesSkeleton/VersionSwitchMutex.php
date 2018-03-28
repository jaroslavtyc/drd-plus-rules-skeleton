<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;

class VersionSwitchMutex extends StrictObject
{

    /**
     * @var string
     */
    private $lockDir;
    /**
     * @var null|resource
     */
    private $lockFileHandle;

    public function __construct(string $lockDir = null)
    {
        $this->lockDir = $lockDir ?? \sys_get_temp_dir();
    }

    /**
     * @param int $wait
     * @return bool
     * @throws \DrdPlus\RulesSkeleton\Exceptions\CanNotWriteLockOfVersionMutex
     * @throws \DrdPlus\RulesSkeleton\Exceptions\CanNotLockVersionMutex
     */
    public function lock(int $wait = 2): bool
    {
        $waitUntil = \time() + $wait;
        $locked = null;
        $handle = $this->getLockFileHandle();
        $attempts = 0;
        do {
            $attempts++;
            if ($locked !== null) {
                \sleep(1);
            }
            $locked = \flock($handle, LOCK_EX | LOCK_NB);
        } while (!$locked && \time() < $waitUntil);
        if (!$locked) {
            $this->unlock(); // closes file handle
            throw new Exceptions\CanNotLockVersionMutex(
                "Even after {$wait} seconds and {$attempts} attempts the lock has not been obtained on file {$this->getLockFileName()}"
                . file_get_contents($this->getLockFileName())
            );
        }
        \fwrite($handle, var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));

        return true;
    }

    /**
     * @return resource
     * @throws \DrdPlus\RulesSkeleton\Exceptions\CanNotWriteLockOfVersionMutex
     */
    private function getLockFileHandle()
    {
        if (!$this->lockFileHandle) {
            $this->lockFileHandle = \fopen($this->getLockFileName(), 'ab');
            if (!$this->lockFileHandle) {
                throw new Exceptions\CanNotWriteLockOfVersionMutex(
                    "Can not use {$this->getLockFileName()} as a lock file, can not write to it"
                );
            }
        }

        return $this->lockFileHandle;
    }

    private function getLockFileName(): string
    {
        return $this->lockDir . '/drdplus_rules_version_switch_mutex';
    }

    public function __destruct()
    {
        $this->unlock();
    }

    public function unlock(): bool
    {
        if (!$this->lockFileHandle) {
            return false;
        }
        $unlocked = \flock($this->lockFileHandle, LOCK_UN); // it is no harm to unlock it even if it was not locked
        \fclose($this->lockFileHandle);
        $this->lockFileHandle = null;
        if (\file_exists($this->getLockFileName())) {
            \unlink($this->getLockFileName());
        }

        return $unlocked;
    }
}