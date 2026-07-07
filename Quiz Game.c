#include<stdio.h>
#include<conio.h>
#include<ctype.h>
#include<stdlib.h>
#include<string.h>
void show_record();
void reset_score();
void help();
void edit_score(float , char []);
int main()
     {
     int countr,r,r1,count,i,n;
     float score;
     char choice;
     char playername[20];
     mainhome:
     system("cls");
     printf("\t\t\t QUIZ GAME\n");
     printf("\n\t\t________________________________________");

     printf("\n\t\t\t   WELCOME ");
     printf("\n\t\t\t      to ");
     printf("\n\t\t\t   THE GAME ");
     printf("\n\t\t________________________________________");
     printf("\n\t\t________________________________________");
     printf("\n\t\t   BECOME A MILLIONAIRE!!!!!!!!!!!    ") ;
     printf("\n\t\t________________________________________");
     printf("\n\t\t________________________________________");
     printf("\n\t\t > Press S to start the game");
     printf("\n\t\t > Press V to view the highest score  ");
     printf("\n\t\t > Press R to reset score");
     printf("\n\t\t > press H for help            ");
     printf("\n\t\t > press Q to quit             ");
     printf("\n\t\t________________________________________\n\n");
     choice=toupper(getch());
     if (choice=='V')
	{
	show_record();
	goto mainhome;
	}
     else if (choice=='H')
	{
	help();getch();
	goto mainhome;
	}
	else if (choice=='R')
	{reset_score();
	getch();
	goto mainhome;}
	else if (choice=='Q')
	exit(1);
    else if(choice=='S')
    {
     system("cls");

    printf("\n\n\n\n\n\n\n\n\n\n\t\t\tResister your name:");
     gets(playername);

    system("cls");
    printf("\n ------------------  Welcome %s to C Program Quiz Game --------------------------",playername);
    printf("\n\n Here are some tips you might wanna know before playing:");
    printf("\n -------------------------------------------------------------------------");
    printf("\n >> There are 3 rounds in this Quiz Game,Easy, Meduim & Hard ");
    printf("\n >> In easy round you will be asked a total of 3 questions to test your");
    printf("\n general knowledge. You are eligible to play the game if you give atleast 2");
    printf("\n right answers, otherwise you can't proceed futher to the Meduim and Hard Round.");
    printf("\n    total of 10 questions, in Meduim and Hard Round. Each right answer will be awarded $100,000!");
    printf("\n    By this way you can win upto 2 MILLION cash prize!!!!!..........");
    printf("\n >> You will be given 4 options and you have to press A, B ,C or D for the");
    printf("\n    right option.");
    printf("\n >> You will be asked questions continuously, till right answers are given");
    printf("\n >> No negative marking for wrong answers!");
    printf("\n\n\t!!!!!!!!!!!!! ALL THE BEST !!!!!!!!!!!!!");
    printf("\n\n\n Press Y  to start the game!\n");
    printf("\n Press any other key to return to the main menu!");
    if (toupper(getch())=='Y')
		{
		    goto home;
        }
	else
		{
        goto mainhome;
       system("cls");
       }

     home:
     system("cls");
     count=0;
     for(i=1;i<=3;i++)
     {
    system("cls");
     r1=i;


     switch(r1)
		{
		case 1:
		printf("************\n");
		printf("EASY ROUND");
		printf("\n\nWhich of the following symbol is used to denote a pre-processor statement?");
		printf("\n\nA.!\t\tB.#\n\nC.~\t\tD.;");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='B')
			{
			    printf("\n\nCorrect!!!");count++;
			    getch();
			    break;
}
		else
		       {
		           printf("\n\nWrong!!! The correct answer is B.#");
		           getch();
		       break;
		       }

        case 2:
        printf("************\n");
		printf("EASY ROUND");
		printf("\n\n\nWhich of the following are tokens in C?");
		printf("\n\nA.Constants\t\tB.Keyword\n\nC.Variable\t\tD.All of the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");count++;
			getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D.All of the above");
		       getch();
		       break;}

        case 3:
        printf("************\n");
		printf("EASY ROUND");
		printf("\n\n\nThe operator & is used for");
		printf("\n\nA.Bitwise AND\n\nB.Bitwise OR\n\nC.Logical AND\n\nD.Logical OR");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");count++;
			getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is A. Bitwise AND");
		       getch();
		       break;}

        case 4:
        printf("************\n");
		printf("EASY ROUND");
		printf("\n\n\nCharacter constants should be enclosed between ___");
		printf("\n\nA.Single quotes\t\tB.Double quotes\nC.Both a And b\t\tD.None of these");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");count++;
			getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is A. Single qoutes");
		       getch();
		       break;}

        case 5:
        printf("************\n");
		printf("EASY ROUND");
        printf("\n\n\n String constants should be enclosed between ___");
        printf("\n\nA.Single quotes\t\tB.Double quotes\nC.Both a And b\t\tD.None of these");
        printf("\n\nAnswer: ");
        if (toupper(getch())=='B')
               {printf("\n\nCorrect!!!");count++;
               getch();
                break;}
        else
		       {printf("\n\nWrong!!! The correct answer is B.Double qoutes");
		       getch();
		       break;}

        case 6:
        printf("************\n");
		printf("EASY ROUND");
		printf("\n\n\nThe operator && is an example for ___ operator.");
		printf("\n\nA.A Assignment\t\tB.Increment\n\nC.Logical\t\tD. Rational");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C' )
			{printf("\n\nCorrect!!!");count++;
			getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C. Logical");
		       getch();
		       break;}}
		       }

	if(count>=2)
	{goto test;}
	else
	{
	system("cls");
	printf("\n\nSORRY YOU CAN'T GO TO NEXT ROUND, BETTER LUCK NEXT TIME");
	getch();
	goto mainhome;
	}
     test:
     system("cls");
     printf("\n\n\t*** CONGRATULATION %s you are eligible to play the Game ***",playername);
     printf("\n\n\n\n\t!Press any key to Start the Game!");
     if(toupper(getch())=='p')
		{goto game;}
game:
     countr=0;
     for(i=1;i<=20;i++)
     {system("cls");
     r=i;

     switch(r)
		{
		case 1:
	    printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\nThe continue command cannot be used with");
		printf("\n\nA.Switch\n\nB.do\n\nC.for\n\nD.while");\
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;getch();}
		else
		       {printf("\n\nWrong!!! The correct answer is A. Switch");getch();
		       goto score;
		       break;}

		case 2:
		printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\n Which of the following is not a type of binary operation?");
		printf("\n\nA.Transitive\n\nB.Commutative\n\nC.Associative\n\nD.Distributive");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is A.Transitive");getch();
		      goto score;
		       break;
		       }

        case 3:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\n Choose a right C Statement. ");
		printf("\n\nA.Loops or Repetition block executes a group of statements repeatedly\n\nB.Loop is usually executed as long as a condition is met\n\nC. Loops usually take advantage of Loop Counter\n\nD.All the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D.All the above");getch();
		       goto score;
		       break;}

        case 4:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nLoops in C Language are implemented using?");
		printf("\n\nA.While Block\n\nB.For Block\n\nC. Do While Block\n\nD.All the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {
                printf("\n\nWrong!!! The correct answer is D.All the above");getch();
		       goto score;
		       break;
		       }

        case 5:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nThe conditional operator are also known as");
		printf("\n\nA.Relational operator\n\nB.Binary operator\n\nC.Ternary operato\n\nD.Arithematic operator");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++;getch(); break;}
		else
		       {
		           printf("\n\nWrong!!! The correct answer is C. Ternary operator");
		       getch();
		       goto score;
		       break;
		       }

		case 6:
		printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nWhich of the following operator reverses the result of expression it operators on");
		printf("\n\nA.!\t\tB. ||\n\nC. &&\t\tD.All of the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A' )
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is A.!");goto score;
		       getch();
		       break;}

        case 7:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nIf you have to make decision based on multiple choices, which of the following is best suited?");
		printf("\n\nA.if\t\tB.if-else\n\nC. if-else-if\t\tD.All of the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C. if-else-if");getch();
		       goto score;
		       break;}

        case 8:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nThe continue statment cannot be used with ________");
		printf("\n\nA.for\t\tB.while\n\nC.=do while\t\tD.switch");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++;getch(); break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D. switch");getch();
		       goto score;
		       break;}

        case 9:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nWhat will be the value of y if x = 8?");
		printf("\ny = (x  > 6 ? 4 : 6)");
		printf("\n\nA.Compilation Error\t\tB.0\n\nC.4\t\tD.6");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C.4");getch();
		       goto score;
		       break;}

        case 10:
        printf("************\n");
		printf("MIDIUM ROUND");
		printf("\n\n\nWhich keyword can be used for coming out of recursion?");
		printf("\n\nA.return\t\tB.break\n\nC.exit\t\tD.both A and B");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");countr++;getch(); break;}
		else
		       {printf("\n\nWrong!!! The correct answer is A.return");getch();break;goto score;}

        case 11:
        printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nHow long the following loop runs?");
		printf("\n\nfor(x = 1; x = 3; x++)");
		printf("\n\nA.Three times\t\tB.Four times\n\nC.Forever\t\tD.Never");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
              {printf("\n\nWrong!!! The correct answer is D.Never");getch();
              break;goto score;}
d
        case 12:
        printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nSwitch statement is used to");
		printf("\n\nA.To use switching variable\n\nB.Switch between function in a programchar\n\nC.Switch from one variable to another variable\n\nD.To choose from multiple possibilities which may arise due to different values of a single variable");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			  {printf("\n\nCorrect!!!");countr++;getch();
			   break;}
		else
              {printf("\n\nWrong!!! The correct answer is D.To choose from multiple possibilities which may arise due to different values of a single variable");getch();
              break;goto score;}

		case 13:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nIf switch feature is used, then");
		printf("\n\nA.Default case must be present\n\nB. Default case, if used, should be the last case\n\nC.Default case, if used, can be placed anywhere\n\nD.None of the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++;getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C.Default case, if used, can be placed anywhere");getch();
		       break;goto score;}

        case 14:
        printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nIf the following loop is implemented");
		printf("\n\nvoid main() {");
		printf("\n\nint num = 0;");
		printf("\n\ndo {");
		printf("\n\n- - num;");
		printf("\n\nprintf(“%d”, num);");
		printf("\n\nnum ++;");
		printf("\n\n}");
		printf("\n\nwhile(num >= 0);");
		printf("\n\n}");
		printf("\n\nA. A run time error will be reported\n\nB.The program will not enter into the loop\n\nC.The loop will run infinitely many times\n\nD.There will be a compilation error reported");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C.The loop will run infinitely many times");getch();
		       break;goto score;}

		case 15:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nWhat will be the value of the digit?");
		printf("\n\n#include <stdio.h>\n\nint main() {\n\nint main() {\n\nfor(; digit <= 9; )");
		printf("\n\ndigit++\n\ndigit *= 2;\n\n--digit;\n\n return 0;\n\n}");
		printf("\n\nA.-1\t\tB.17\n\nC.19\t\tD.16");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++;getch();
			 break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D. 16");getch();
		       goto score;
		       break;}

		case 16:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\n What will be the output of the following piece of code?");
		printf("\n\n#include <stdio.h>\n\nint main() {\n\nfor(i = 0;i < 10; i++);");
		printf("\n\n printf(%d, i);\n\nreturn 0;\n\n}");
		printf("\n\nA.10\t\tB. 0123456789\n\nC.Syntax erro\t\tD. Infinite loop");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='A')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;
			}
		else
		       {printf("\n\nWrong!!! The correct answer is A. 10");getch();
		       goto score;
		       break;}


		case 17:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nWhat will be the output of the following piece of code?");
		printf("\n\n#include <stdio.h>\n\nint main() {\n\nint value = 0;\n\nif(value)\n\n printf(well done);");
		printf("\n\nprintf(Algbly);\n\nreturn 0;\n\n}");
		printf("\n\nA.well done AlgblY\t\tB.Algbly\n\nC. complier error\t\tD.None of these");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C. complier error");getch();goto score;
		       break;}

		case 18:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\ndo-while loop terminates when conditional expression returns?");
		printf("\n\nA.One\t\tB.Zero\n\nC.Non - zero\t\tD.None of the above\n\n");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='B')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is B.Zero");getch();goto score;
		       break;}

		case 19:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nSwitch statement accepts");
		printf("\n\nA. int\tB.char\n\nC.long\tD.All of the above\n\n");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D.All of the above");getch();goto score;
		       break;}

		case 20:
		printf("************\n");
		printf("HARD ROUND");
		printf("\n\n\nWhich loop is guaranteed to execute at least one time?");
		printf("\n\nA. for\t\tB.while\n\nC.do while\t\tD.None of the above");
		printf("\n\nAnswer: ");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C.do while");getch();goto score;
		       break;}

		case 21:
		printf("\n\n\nOzone plate is being destroyed regularly because of____ ?");
		printf("\n\nA.L.P.G\t\tB.Nitrogen\n\nC.Methane\t\tD. C.F.C");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D. C.F.C");getch();goto score;
		       break;}

		case 22:
		printf("\n\n\nWho won the Women's Australian Open Tennis in 2007?");
		printf("\n\nA.Martina Hingis\t\tB.Maria Sarapova\n\nC.Kim Clijster\t\tD.Serena Williams");
		if (toupper(getch())=='D')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is D.Serena Williams");getch();goto score;
		       break;}

		case 23:
		printf("\n\n\nWhich film was awarded the Best Motion Picture at Oscar in 2010?");
		printf("\n\nA.The Secret in their Eyes\t\tB.Shutter Island\n\nC.The King's Speech\t\tD.The Reader");
		if (toupper(getch())=='C')
			{printf("\n\nCorrect!!!");countr++; getch();
			break;}
		else
		       {printf("\n\nWrong!!! The correct answer is C.The King's Speech");getch();goto score;
		       break;}}}
	score:
    system("cls");
	score=(float)countr*100000;
	if(score>0.00 && score<2000000)
	{
	   printf("\n\n\t\t**************** CONGRATULATION *****************");
	     printf("\n\t You won $%.2f",score);goto go;}

	 else if(score==2000000.00)
	{
	    printf("\n\n\n \t\t**************** CONGRATULATION ****************");
	    printf("\n\t\t\t\t YOU ARE A MILLIONAIRE!!!!!!!!!");
	    printf("\n\t\t You won $%.2f",score);
	    printf("\t\t Thank You!!");
	}
	 else
{
	 printf("\n\n\t******** SORRY YOU DIDN'T WIN ANY CASH ********");
	    printf("\n\t\t Thanks for your participation");
	    printf("\n\t\t TRY AGAIN");goto go;}

	go:
	puts("\n\n Press Y if you want to play next game");
	puts(" Press any key if you want to go main menu");
	if (toupper(getch())=='Y')
		goto home;
	else
		{
		edit_score(score,playername);
		goto mainhome;}}}

void show_record()
    {system("cls");
	char name[20];
	float scr;
	FILE *f;
	f=fopen("score.txt","r");
	fscanf(f,"%s%f",&name,&scr);
	printf("\n\n\t\t*************************************************************");
	printf("\n\n\t\t %s has secured the Highest Score %0.2f",name,scr);
	printf("\n\n\t\t*************************************************************");
	fclose(f);
	getch();}

void reset_score()
    {system("cls");
    float sc;
	char nm[20];
	FILE *f;
	f=fopen("score.txt","r+");
	fscanf(f,"%s%f",&nm,&sc);
	sc=0;
	fprintf(f,"%s,%.2f",nm,sc);
    fclose(f);}

void help()
	{system("cls");
    printf("\n\n                              HELP");
    printf("\n -------------------------------------------------------------------------");
    printf("\n ......................... C program Quiz Game...........");
    printf("\n\n Here are some tips you might wanna know before playing:");
    printf("\n -------------------------------------------------------------------------");
    printf("\n >> There are 3 rounds in this Quiz Game,Easy, Meduim & Hard ");
    printf("\n >> In easy round you will be asked a total of 3 questions to test your");
    printf("\n general knowledge. You are eligible to play the game if you give atleast 2");
    printf("\n right answers, otherwise you can't proceed futher to the Meduim and Hard Round.");
    printf("\n    total of 10 questions, in Meduim and Hard Round. Each right answer will be awarded $100,000!");
    printf("\n    By this way you can win upto 2 MILLION cash prize!!!!!..........");
    printf("\n >> You will be given 4 options and you have to press A, B ,C or D for the");
    printf("\n    right option.");
    printf("\n >> You will be asked questions continuously, till right answers are given");
    printf("\n >> No negative marking for wrong answers!");
	printf("\n\n\t*********************BEST OF LUCK*********************************");
	printf("\n\n\t*****C PROGRAM QUIZ GAME is developed by CODE WITH C TEAM********");}

void edit_score(float score, char plnm[20])
	{system("cls");
	float sc;
	char nm[20];
	FILE *f;
	f=fopen("score.txt","r");
	fscanf(f,"%s%f",&nm,&sc);
	if (score>=sc)
	  { sc=score;
	    fclose(f);
	    f=fopen("score.txt","w");
	    fprintf(f,"%s\n%.2f",plnm,sc);
	    fclose(f);}}


