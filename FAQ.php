<?php
include('header.php');

//if (isloggedin())
//{
?> 
<h2>F.A.Q.</h2>
<h3><b>How does it work?</b></h3>
<p>This website is part of a study in eGovernment. We are studying the possibilities of a group of people to brainstorm together the solution to a common question.</p>
<h3><b>Can I ask any question I want?</b><br></h3>
<p>This website is studied to really explore the different possible alternative answer to an open question, and find an answer endorsed by as many people as possible (theorethically everybody). As such the question should be an open question.</p>
<h3><b>What is an open question?</b></h3>
<p>An open question is a question whose answer is not implicit in the question. Generally open questions start with "how", "why", "what shall".</p>
<p>Par contro a closed question is a question where the answers are already given (implicitly or explicitly). Open questions invite the person who is answering them to think, and can bring on unexpected results. As such open questions are good educative tools, but are often feared for they give a lot of power to the person who is answering them.</p>
<h3><b>What is a room?</b></h3>
<p>When you ask a new question you are given the option to assign it to a room. You can name the room (alpha-numeric characters and underscores only) or generate a random name - good if you want privacy. Only people who know the room will see your question. For the moment, in order to view a question in a room you need to enter the question number and the room parameter in the URL, eg</p>
<p>http://vilfredo.org/viewquestion.php?q=67&room=vilfredo</p>
<p>However, the people you invite to submit proposals will be see your new question listed on their ToDo List page.</p>
<h3><b>What are generations?</b></h3>
<p>To make sure that each answer competes with the others in a fair way we need to let everybody chose which of them to endorse at the same time, and stop at the same time. So we need to have two phases, a writing phase, where everybody can write their proposals, and an endorsing time, where everybody is allowed to endorse each other questions (and its own's as well). A writing phase followed by an endorsing phase constitute a generation</p>
<h3><b>what does it mean to "ignore vs endorese"?</b></h3>
<p>I don't like the idea that you rate negatively something there are many reasons why you don't want to endorse something, but if we start to divide them we become crazy and it is useless. So the axe came down quite neatly, either you support something or you do not. If you do not, I don't care if you hate it, or you just dislike it a bit. I just want to know what is that you support. The others are ignored</p>
<h3><b>How many answers can I endorse?</b></h3>
<p>As many as you want. In fact for the system to work well you should endorse <i>all</i> the answers you actually agree with.</p>
<h3><b>I asked a question, but now I received more than 1 answer. What does it mean?</b></h3>
<p>That the system has not finished working. Those answers will be used to seed the next generations. Now everybody can read those answers, and be inspired by them, trying to find an acceptable compromise between them, that can be endorsed by both sides. When this is found the new solution will substitute the old ones</p>
<h3><b>Can't you just pick the answer that is endorsed by more people? Why do you always need to make things complicated?</b></h3>
<p>The fact that more people endorse an answer does not make it necessarily the best. It only means that if we were to fight, those people could impose their point of view. In this website we are trying to find an answer by considering everybody's point of view. The assumption is that there is an answer out there, and we need to find it. The assumption is not necessarly always true, but often it will be</p>
<h3>What is the internal algorithm that defines how an answer is chosen over another?</h3>
<p>We are preparing a paper to describe this. Once the paper will be presented (probably at a conference this year) we shall refer to it, and will be able to read all the details</p>
<h3>Why is this website called, Vilfredo goes to Athens?</h3>
<p>Part of the theory that we used to produce the algorithm was developed by Vilfredo Pareto, an Italian Economist of the last century. Since we are using his theory to develop an e-government website, we figured out that this website would be the product of an hipothethical trip of Vilfredo to Athens' Agorà. The name is also obviously inspired by the Frankie Goes to Hollywood music band.
</p>
<h3>What do the icons (apple, tree, etc.) represent?</h3>
<p>Each icon means something different. Generally if you move with the mouse over the icon, it should appear a hint of what it means. Still we can review them here. </p>
<ul><li><img src="http://vilfredo.org/images/germinating.jpg" height="36"> The <span style="font-weight: bold;">germinating plant</span> means that a question is on its first generation. It has no proposals inherited from the previous round and is waiting for the first set of proposals to be written, before going to the first endorsing phase. </li>
<li><img src="images/tree.jpg" height="36"> The <span style="font-weight: bold;">tree</span> means that a question is in its second or subsequent generation, and it has some proposals inherited from the previous generation.</li>
<li><img src="http://vilfredo.org/images/flowers.jpg" height="36"> The <span style="font-weight: bold;">flowers</span> means that a question is ready to be voted on. </li>
<li><img src="http://vilfredo.org/images/apple.jpg" height="24"> <img src="http://pareto.ironfire.org/images/fruits.jpg" height="24"> The <span style="font-weight: bold;">fruits</span> mean that the question has reached some proposals endorsed by everybody. If everybody agreed on a single proposal, the apple will appear, if instead multiple answers have all been endorsed by everybody, then the multiple fruit will appear.</li>
<li><img src="http://vilfredo.org/images/tick.jpg" height="24"> The <span style="font-weight: bold;">Ticked Box</span> means that the user that has logged in has acted on this question, either endorsing the proposals written, or by writing new proposals. </li>
<li><img src="http://vilfredo.org/images/tick_empty.png" height="24"> Viveversa the <span style="font-weight: bold;">Unticked Box</span>  means that the user has not acted so far, and is thus invited to do so.</li>
<li><img src="http://vilfredo.org/images/email.png" height="12"> The little letter near a name means that the user is receiving email updates, and thus will probably keep on participating in the future. This is useful to divide between questions and proposals written by people who are likely to follow them up, and questions and proposals that will probably just be forgotten.</li></ul>
<h3>When does a discussion ends ?</h3>
<p>Basically a discussion ends, when everybody is satisfied with the result, and thus does not post anything else. But why would this be the case? There are a vew possibilities, let's look at them one by one. Of course the best way for it to end is with a single result, of a proposal endorsed by everybody. If everybody agrees on the result, no one proposes anything else, and the result is considered final. Anoher possibility is when there are more than one proposal, but each proposal has been endorsed by everybody. And each proposal mean pretty much the same thing. Just rephrased in a different way. If this is the case it is possible that no one will post anything else. At this point the result is known, and what has remained to be done is to phrase it in the best possible way. Something which can be done with this tool, but probably would be better done directly with a wiki. The next possibility is when there are more proposals, and they do not mean the same thing. This tend to have the meaning that the community have found that either of those proposals, or even all of them are ok. Sometimes it is not possible to find an agreement. Eventually the discussion still will end when the people are not participating on it anymore. Once the situation is clear, the positions might reach a sort of standby where no new proposal is being written, and everybody keep on voting on the same old position. When this is the case the discussion has ended. And what you have is a picture of a divided society. Still it might be possible in future that someone might try to workout an acceptable compromise. You should always remember that it is easier to find a compromise when two person are really disagreeing on a particular issue. It can instead be impossible if is just voting for its own proposals, just to show that he does not agree with the others.</p>
<h3><a name="email"></a><b>Why do I have to insert my email address?</b></h3>
<p>This site requires a semi continuous interaction for each question. In the sense that after you have answered a question, you still need to endorse that question, and endorse all the other proposals that make sense to you. Then once the voting phase is over, a set of answers will be generated. At that point you can try to suggest possible new answers that permit an agreement between the existing groups. All this requires that multiple interaction, as after having voted, you need to wait for everybody else to vote as well, before going on to the next phase, etc...It is for this reason that we ask, your email address. Every time one of the question where you have recently (in the last two phases) proposed or voted goes to the next phase, you will receive a short email, with the link to the posed question. You can also require to receive updates about a question even if you haven't interacted with it yet. The button for this is in the question page, near the top.</p>
<h3><b><a name="bugendorsmen"></a>I remember in question x, on generation y, I endorsed proposal z. But now it does not appear in the history. Why?</b></h3>
<p>Because there was a bug. Unfortunately we had a bug that deleted some of the past endorsments. It never acted on the last generation, and it never changed who the winner was for a generation (as it acted AFTER the algorithm has calculated the winner). Unfortunately the result of this is that now if you look at the history of some proposals you cannot understand why some proposals won over others. Shit happens, and this is why the system is still in Alpha. Yet we have corrected the bug, and now it is not bugging us anymore. Unfortunately the data gone is gone. </p>
<h3><b>What is the time frame allocated to a generation ? In other words, how long does one generation take ? Should I be checking back hourly, daily, etc. ?</b></h3>
<p>It's a mystery ;-). At the current state of the art each question will move on when the questioner (the person who wrote the question), will chose to move it on. This can only be done when there are at least 2 different proposals to be endorsed or at least one person has voted. But sometimes, some users, either forget that they have asked the question, or want more people to participate, and thus let a question sit there and wait. This is acceptable from the point of view of the algorithm, but of course it means that the other users have to wait as well. To simplify the whole situation we have added automatic emails. Now every time the questioner moves on, each user who has participated in the last generation (i.e. the last voting phase, and the last endorsing phase) will receive an email, making them aware that they need to act again. Also now every time a user endorses something in a question for the first time in a generation, or writes a proposal for the first time in a generation, the questioner will receive an email. In this way the questioner never fully looses sight of the question. And will come back and move it on, when it is the right moment. We are considering making some of the moveon automatic. But it is something which can very easily be done uncorrectly, so we are proceeding very cautiously.</p>

<h3><a name="whyproposalhidden"></a>OK, so I just created a proposal and it says that other people have also written some proposals. Where are they? Why can't I see them?</h3>
<p>They will appear in the endorsing phase. There are many reasons why the proposals are not shown immediately:</p>
<ul>
<li>to push everybody into thinking a question over without just repeating what the first user is saying</li>
<li>to make sure that it is then possible to track down who was the first person to present a particular idea</li>
<li>To give to each user a fair possibility in writing their idea, editing it while they wait for the question to be discussed</li>
</ul>
<h3><a name="howdoiproposeduringendorsement"></a>I found a question I'm interested in, and it's in the endorsement phase. How do I make a proposal? </h3>
<p>Essentially you lost the train and you need to wait until is back in the writing phase. But you need to make sure that you catch the next train, i.e. that the question does not go to the writing phase, and then back to the endorsing phase without you having had a chance to write your idea. You can do this by either joining during the endorsing phase, endorsing the proposals you agree with, or by activating the email update (on the top of the question page). In both cases, next time the question moves on to the writing phase you will receive an email. Of course make sure your email is correct. We might eventually permit to users to write new proposals during the endorsing phase. They will still only appear during the next endorsing phase. But it would take away some of the sense of time that comes out of this strong metronome. Also it might disempower the voting process done, as the pareto front becomes less important. So we are reluctant to do it</p>

<?php

/*
}
else
{
		header("Location: login.php");
}*/

include('footer.php');

?> 