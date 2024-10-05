import os
import subprocess
from crontab import CronTab

def add_hourly_cron_job(command):
    # Create a cron object for the current user
    cron = CronTab(user=True)  # or specify username like CronTab(user='your-username')

    # Check if the command already exists in the crontab
    job_exists = any(job.command == command for job in cron)

    if job_exists:
        print("This command is already scheduled in your crontab.")
    else:
        # Create a new job to run every hour
        job = cron.new(command=command)
        job.minute.on(0)  # Run at the start of every hour
        cron.write_to_user(user=True)  # Save the cron job for the current user
        
        print("The command has been added to your crontab to run every hour.")

if __name__ == "__main__":
    # Replace this with your desired command or script
    command = "/path/to/your-command-or-script"
    
    # Call the function to add the cron job
    add_hourly_cron_job(command)
