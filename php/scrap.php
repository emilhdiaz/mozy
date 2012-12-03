<?php

echo "Please enter your age: \n";

$age = trim(fgets(STDIN));

echo "You are $age year(s) old\n";

#sleep(5);

die();

/*
function launchchild() {
    var_dump("here");
    $pid = pcntl_fork();
    if ($pid == -1) {
        die('could not fork');
    }
    else if ($pid) {
        // we are the parent
        print "Parent";

        pcntl_wait($status); //Protect against Zombie children
    }
    else {
        // we are the child
        print "Child";
    }
}

launchchild();
*/

$MEMSIZE = 512; //  size of shared memory to allocate
$SEMKEY = 1;   //  Semaphore key
$SHMKEY = 2;   //  Shared memory key

echo "Start.\n";

// Create a semaphore
$sem_id = sem_get($SEMKEY, 1);
if ($sem_id === false)
{
    echo "Failed to create semaphore";
    exit;
}
else
    echo "Created semaphore $sem_id.\n";

// Acquire the semaphore
if (! sem_acquire($sem_id))
{
    echo "Failed to acquire semaphore $sem_id.\n";
    sem_remove($sem_id);
    exit;
}
else
    echo "Success acquiring semaphore $sem_id.\n";

// Attach shared memory
$shm_id = shm_attach($SHMKEY, $MEMSIZE);
if ($shm_id === false)
{
    echo "Fail to attach shared memory.\n";
    sem_remove($sem_id);
    exit;
}
else
    echo "Success to attach shared memory : $shm_id.\n";

// Write variable 1
if (!shm_put_var($shm_id, 1, "Variable 1"))
{
    echo "Failed to put var 1 in shared memory $shm_id.\n";

    // Clean up nicely
    sem_remove($sem_id);
    shm_remove($shm_id);
    exit;
}
else
    echo "Wrote var1 to shared memory.\n";

// Write variable 2
if (!shm_put_var($shm_id, 2, "Variable 2"))
{
    echo "Failed to put var 2 on shared memory $shm_id.\n";

    // Clean up nicely
    sem_remove($sem_id);
    shm_remove ($shm_id);
    exit;
}
else
    echo "Wrote var2 to shared memory.\n";

// Read variable 1
$var1 = shm_get_var($shm_id, 1);
if ($var1 === false)
{
    echo "Failed to retreive Var 1 from Shared memory $shm_id, " .
         "return value=$var1.\n";
}
else
    echo "Read var1=$var1.\n";

// Read variable 1
$var2 = shm_get_var ($shm_id, 2);
if ($var1 === false)
{
     echo "Failed to retrive Var 2 from Shared memory $shm_id, " .
          "return value=$var2.\n";
}
else
    echo "Read var2=$var2.\n";

// Release semaphore
if (!sem_release($sem_id))
    echo "Failed to release $sem_id semaphore.\n";
else
    echo "Semaphore $sem_id released.\n";

// Remove shared memory segment
if (shm_remove ($shm_id))
    echo "Shared memory successfully removed.\n";
else
    echo "Failed to remove $shm_id shared memory.\n";

// Remove semaphore
if (sem_remove($sem_id))
    echo "Semaphore removed successfully.\n";
else
    echo "Failed to remove $sem_id semaphore.\n";

echo "End.\n";
?>