<?php

namespace app\models\logic;

use app\components\CacheConst;
use app\components\CommonConst;
use app\components\log\Log;
use app\helpers\CsvHelper;
use app\models\data\AdminUserData;
use app\models\data\AnswerData;
use app\models\data\UserData;

/**
 * @uses     AdminLogic
 * @version  2019年09月08日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class AdminLogic extends Logic
{

    private $adminData;
    private $userData;
    private $answerData;

    public function __construct()
    {
        $this->adminData  = new AdminUserData();
        $this->userData   = new UserData();
        $this->answerData = new AnswerData();
    }

    private static $expire = 3600 * 12;

    //每项最高评语
    const TOTAL_BEST_REMARK         = '此类受测者的心理素质非常优秀，超过了90%的人。他们总是能出色地完成工作目标，找到问题的多个解决方案，并从中选择出最优方案。他们有健康的情绪，与人为善的性格，出色的自我调适能力，不仅在任何团队中都能够很好地履行团队成员的职责，还很有可能成为团队的领导者。此类受测者对自我的要求颇高，他们认为自己完全可以变得更好，困难和挫折在他们看来是人生的礼物，他们特别认同“梅花香出苦寒来”的说法。对于他们来说，诚信守诺是一个人重要的品质，答应过的事情就一定要做好，他们在结交朋友时也特别看重这一点。我们认为，对于心理素质能够达到人群中前10%水平的此类受测者来说，事业上的成功是早晚的事，他们就像金子一样，到哪里都能闪闪发光。';
    const FRUSTRATION_BEST_REMARK   = '此类受测者有着出色的耐挫抗压能力，他们在人群中所占的数量不会超过10%。他们几乎不会受到压力带来的困扰，也不太可能被压力所束缚。一般来说，他们沉稳坚定，在绝大部分困难或压力面前都能保持一颗平常心。他们也不惧挑战，甚至喜欢挑战，因而他们中的一些人以解决难题为乐，从战胜挑战和解决问题中收获成就感。在团队中因为沉稳坚定，他们往往能影响其他成员，使他们平静下来面对挑战和压力。';
    const RESPONSIBLE_BEST_REMARK   = '对于此类受测者来说，责任与自己的人品几乎是同义词，承诺对于他们来说有着极其重要的意义。由于常常将团队的责任视为自己的责任，所以无论身处何地，此类受测者都能很快融入团队成员，以主动的姿态进入工作。他们的内驱力主要来自他们内心比一般人要强烈得多的责任感，因此他们的成就也往往来自于类似于“这是我的责任”、“我说过的就一定要做到”这样的信念。他们不能理解缺乏责任心的同事，对于经常不守承诺的朋友他们也会敬而远之。了解他们的人常常会给予他们“靠谱”、“靠得住”的评价，而他们也认为这是自己身上最出色的一点，以此为荣。';
    const DEBUGGING_BEST_REMARK     = '此类受测者是少数的乐观派，仅占人群的10%。他们有较强的自我调节能力，可以在多种情况下从容不迫、不乱阵脚。此类受测者中有些是天性如此，有些则是经过长期训练的结果。他们几乎可以面对任何情况，在突发事件面前他们往往是最沉着不变的那一个。他们的从容常给人信心，他们的乐观常让人得到安慰。';
    const ASSISTANCE_BEST_REMARK    = '此类受测者认为团队是工作中极其重要的关键因素，他们很重视团队，非常清楚团队对于自我发展的作用。擅长沟通一直都是此类受测者的优点，他们处理团队中的各种关系都游刃有余，懂得分寸，总是能够凝聚起团队，并在团队中展现出优秀的一面。如果他们刚好具有一定水平的专业能力，他们中的多数人会自然而然成为团队的领导者。对于此类受测者来说，没有团队是不可想象的，他们始终将团队放在第一位，有时甚至会为了团队牺牲一些个人的利益。  ';
    const SELF_EFFICACY_BEST_REMARK = '此类受测者的人生词典里没有“我不行”、“我做不到”这样的话语，他们对自己说的最多的话是“怎样才可以？”“要实现那个目标，下一步我要做什么？”……在工作中，他们不仅会选择适合于自己能力水平的任务，他们还会试着去做一些富有挑战性的工作，而且他们从来都认为自己成功的可能性非常大。对于他们来说，困难是磨练，而不是应该避免的威胁；即使失败，原因也是自己的努力还不够，或是自己的运气还差一点，而不是因为自己的能力不行或是天资太差。他们总是把注意力集中在积极分析问题和解决困难上，知难而上、执著追求，常常超常发挥，将“不可能”变成“我做到了”或“我们做到了”。
有时在同事或亲友眼里，他们可能会显得有些过分自信，太喜欢冒险，但在他们自己看来，人生就是要不断前进，不断冒险，不断折腾，因为“我做得到！”';

    //总体
    const TOTAL_CONTENT = [
        60 => '此类受测者的心理素质整体相对较弱。在情绪情感方面，他们较为敏感，比较容易沉溺于消极情绪，一旦遭受生活或工作上的打击，调整情绪状态对他们来说往往会格外困难。在自我认知方面，他们有过度贬低自己的倾向，因此在工作时会选择更容易完成的任务。在团队中他们没有很强的存在感，一是因为他们不认为团队有多重要，二是因为他们不太愿意与团队成员交流，这两者会让他们更少地参与团队互动和沟通。在别人眼中，他们或许是内向、被动的人，但在他们自己眼中并不是这样。他们更喜欢一成不变、有规律的生活和工作方式，看不到清晰的未来和结果会让他们感到焦虑。',
        70 => '此类受测者的心理素质一般，达到基本水平。他们可能在前述五个因素中同样有达到基本水平的表现，也可能在某个因素上表现突出，而在别的因素上表现得不好。不论是属于哪一种情况，此类受测者的整体心理素质水平都需要进一步提高。相对高分人群而言，一般来说，这一类受测者在情绪情感表达上会更加退缩，不太能够适应多变的环境，处理复杂人际关系的能力一般，面对压力和困难时他们常常会内心焦虑。他们中的一些人意识到自己的问题，很想要去改变，但经常会立下Flag却中途放弃。这一类受测者需要明白，他们已经达到了一个基本水平，只需要有意识地努力，再辅之以适当的方法，假以时日，他们就能变成另外一个样子。对于他们来说，要改变的话，首先需要的是将最重要的问题从“要不要开始”改为“第一步我要做什么？”',
        79 => '此类受测者的心理素质为良好。对于此类受测者来说，健康生活、快乐工作他们所追求的，虽然遇到困难时他们也会有紧张、焦虑，但大多数情况下他们能够自我调整。他们能配合团队开展工作，也能在团队中与其他成员沟通，在同事看来，他们是“可以相处”的那一类人。对于上级交代的工作任务，他们一般都能够如期完成。他们并不特意在追求工作上尽善尽美，而是希望工作和生活能够尽量平衡。在选择工作任务时，他们很可能会选择那些适合他们水平的，有时甚至是偏简单的工作。总体来看，他们有较为健康的情感，一定的自我调适能力，对待生活和工作也有较为积极向上的态度，这一切都为他们将来的发展打下了基础。',
        89 => '此类受测者的心理素质比较优秀。在压力面前，他们能轻松应对，面对困难或突发事件也能较快地调整好自己的情绪状态，因此他们可以胜任那些要求具有一定应变能力的工作。在团队中他们常常是凝聚力所在，他们也能从团队中得到力量和帮助。他们对自己的能力有相当的信心，相信事在人为，不太会把失败归结于自己的天资不够，而是认为只要努力就一定会有收获。他们负责任的个性特征会让同事和领导感到放心，不过他们在主动性上确实还有进一步提升的空间。',
    ];
    //抗挫能力分数
    const FRUSTRATION_CONTENT = [
        185 => '此类受测者对压力比较敏感，忍耐的能力较为脆弱。当压力积压于一身时，他/她往往不能及时舒缓，很难做到心平气和。他/她常常会认为“问题能否解决似乎是一个未知数”，这样的想法会让他/她烦躁不安，因此他/她在面对生活或工作上的问题时大部分时候都会不知所措。此时受测者最需要做的是静下心来梳理问题，沉着思考，同时在有外力协助的情况下，依靠设置某项硬性目标来帮助他/她解决问题。',
        192 => '在处理与压力有关的事情时，此类受测者总会发现自己在开始新的计划或按原来的计划要进行下一步任务时会犹豫不决。这种情况绝大多数是由他/她面对压力时产生的不良情绪带来的，因为他/她直觉地认为压力是不好的东西。不仅如此，在潜意识里他/她还一直想要与压力做斗争。这样一来，他/她常会发现自己受情绪影响，无法集中注意力在开展新的计划或继续原有计划上。而计划受阻又会产生新的压力，新的压力带来新的情绪困扰，新的情绪困扰又一次地使此类受测者停止行动，他/她便陷入了压力——情绪困扰——停止行动——新的压力——新的情绪困扰——再次停止行动的恶性循环。',
        200 => '在日常生活和工作中，此类受测者能表现出一定的耐挫抗压能力，他/她能在问题面前表现出较为积极的态度，一般情况下不会一心只想逃避问题。他/她可能会由于一直处于波澜不惊的生活状态而以为自己具有很强的耐挫抗压能力，这种认知会使他/她对挫折或压力缺乏敏感性，因而在面对突发情况或显然超过自己能力或权责范围的问题时，他们往往会感到心跳加速，呼吸急促，大脑一片空白。另一方面，此类受测者的耐挫抗压能力受到他/她对目标渴求程度的较大影响，若他/她对目标的渴求非常强烈，他/她会觉得挫折或问题无法忍受。',
        212 => '在面对棘手的状况时，你能通过自我调节迅速冷静下来，使自己的思维不受压力干扰。你的心态平稳，极少受到外界干扰，再加上你对于解决问题有坚定的信念，因此你有相当良好的行动力，常常是说了就马上去做。行动能缓解压力带来的紧张和焦虑，行动也会让你找到解决问题的方向。人生的问题虽然多，但良好的耐挫力已经让你成功了一半。在工作和生活中，你都会比别人承担更多的压力，因为在面对高强度的工作，或非常麻烦的问题时，你在绝大部分时候都表现出比别人更好的耐挫力，因而你身边的人会认为你是一个能承受挫折和压力的人。在身边人看来，乐呵呵面对问题是你乐观的体现，只有你自己明白，做到考虑周全才是沉着冷静的资本。都说人生事事难料，少数情况下突发状况会阻挠你的下一步行为，有的时候，慢慢停下来，放缓解决问题的步伐，心急可吃不了热豆腐，压力较大的问题或许对你来说，未尝不是件坏事儿，难得的心理冲突会让你在团队中显得更有存在感。',
    ];
    //责任心分数
    const RESPONSIBLE_CONTENT = [
        67 => '因为某些我们还不能确切知道的原因，此类受测者在工作中极少会有归属感，他们对工作不乐意过多付出，虎头蛇尾的情况常会发生在此类受测者身上。相对一成不变和刻板的生活和工作，他们更喜欢追求自由不受约束的生活和工作，不愿意被承诺所困，也不在意承诺的意义。在风险和困难面前，此类受测者往往不以为然，轻看风险使他们可能会在没有细致思考的情况下就行动。同时，此类受测者不会主动替自己将责任揽在身上，出了问题也不会认为自己有什么必须承担的责任，因而他们常常感觉轻松愉快。',
        71 => '拥有轻松的工作是此类受测者享受生活的一部分，工作对于他们的意义或许并不是非常重要。他们在对待工作时比较容易情绪化，因此在与同事一起工作时可能会出现问题。此类受测者一般对于工作的权责有一定的认识，对于自己工作的范围他们有一定了解，在此范围之外的责任他们一般不愿意承担。即使是有晋升的可能，此类受测者中的大部分人也不会主动承担责任。在此类受测者看来，责任并不是最重要的因素，生活稳定、工作富有乐趣是更重要的东西。',
        73 => '此类受测者认为完成工作是天经地义的事，所以他们对于分配到他们头上的工作一般能完成，哪怕加班加点也不会有太多的抱怨。工作对他们来说是生活中不可少的一部分，同时他们也乐意将更多的时间花在家庭、个人爱好等这类与工作无关、同样也是生活必不可少的部分。他们不会主动承担太多不属于自己的责任，也很少会强烈感觉到自己对别人、对所在团体有责任。对他们来说，责任常常意味着某种不得不为之事，是有强制性的。',
        76 => '此类受测者的责任感超过80%的人，如果再配之以较强的工作能力，他们会得到比同岗位的其他人更快的提拔，更受领导重用。他们比较容易在团队中找到归属感，愿意承担责任，而且常会承担一些超出所在的工作岗位权责范围的责任。在对待和家人、朋友的关系时，他们认为信守承诺是重要的，所以他们给人的印象一般是守信靠谱的。此类受测者对工作的热情更多地来自责任感，他们颇为认同“坚持就是胜利”、“付出总会有收获”之类的说法。',
    ];

    //心理调试能力分数
    const DEBUGGING_CONTENT = [
        58 => '人生不可避免地存在着各种矛盾和问题，此类受测者对于这些矛盾和问题常常感到束手无策，最大的念头就是想尽早逃走。造成这种情况原因主要是他们在矛盾和问题前思维混乱，情绪波动大，无法快速适当地进行自我调节。',
        60 => '此类受测者面对工作或生活中的突发状况往往不能及时调整自己的情绪，也无法听从他人的劝告以转变自己的心态为首要任务。虽然他们很想能够像一些人那样很好地应对困境，但他们就是会被压力和挫折极大地限制住。他们常常希望世界不要有任何变化，这样自己就不会遇到什么问题。',
        63 => '面对问题和挫折，此类受测者会想通过自我调节来努力使自己平静，他们对自己的情绪感受有一定的关注，明白自我调节的重要内容是要先调整好自己的情绪。他们有一些属于他们自己的自我调节方法，比如逛街、看电影、打游戏等，他们会有意识地使用这些方法来转移他们的注意力，使自己的情绪暂时不会被问题和挫折所困扰。不过严格来说，这些方法只是暂时回避了问题，每当此类受测者意识到问题并没有被解决，他们还是会产生烦躁、低落的情绪。',
        67 => '辛勤工作是此类受测者一直以来的工作状态，此类受测者总是对工作兢兢业业，对于这一点，此类受测者的领导和同事都是有目共睹。工作出现大大小小的漏洞是常有的事，虽然有时会影响到此类受测者的积极性，但是此类受测者能比大约80%的人更快地调整好自己的心态。他们有自我调整的方法，能帮助他们面对暂时的问题和挫折，同时他们也相信“世上无难事，只怕有心人”，在他们看来，一时的困难不算什么，只要坚持不懈地努力，问题总会被解决。',
    ];

    //团队协助能力分数
    const ASSISTANCE_CONTENT = [
        44 => '此类受测者会有意识地回避团队协作，因为他们担心团队会阻碍自己的思考，害怕自己在团队中会人云亦云，失去自我。他们也不喜欢团队中必须存在的沟通，因为他们认为这样会拉低效率，并使简单的事情复杂化。他们早已习惯独自处理工作和生活中的事务，并且长时间以来都是这样，他们早就将其视为理所当然的事。',
        46 => '此类受测者在团队中的存在感往往并不强，相较而言，他们更愿意选择独自工作，而不是与他人合作。由于某些因素的影响，他们没有清楚地认识到团队的作用，常常以为单凭自己的能力就能够轻松完成工作，不需要团队协助。事实上团队才是他们需要学习的功课，他们需要更多地认识团队的作用，学会如何与团队成员沟通、如何在团队中实现自我成长。',
        47 => '此类受测者懂得并会遵守团队合作的规范，有一定的团队协作能力。不过，尽管大多数情况下他们会依据团队的规则做事，但是他们还会有要脱离团队，独自完成工作的想法。之所以会这样是因为从根子上他们并不认同与人合作能够帮助他们实现自我发展，也不真的认为团队合作的效率更高，他们只觉得做好自己该做的才是理所当然的，团队在某种意义上被他们视为完成工作目标的可选工具。',
        48 => '此类受测者能较好地与团队成员沟通，并较好地融入团队。虽然有时在工作中他们还是会与团队成员发生矛盾，但总的来说团队对他们而言有较强的吸引力，在大多数情况下，他们也能较好地协调与团队其他成员的关系，以保证团队合作能顺利进行。此类受测者常常因为团队合作而受益，对他们而言团队是工作中较为重要的部分，因此他们会认为花时间与团队在一起是有必要的。',
    ];

    //自我效能感分数
    const SELF_EFFICACY_CONTENT = [
        44 => '此类受测者面对困难的任务时首先会怀疑自己能力，先考虑自身的缺点与将面临到的阻碍，详列出各种可能导致负面结果，而非思考克服或成功的方法。面对失败，会认为是因为自己能力不足以克服所有导致失败的负面因素，进而倾向放弃并减少努力。因此，相比其它人而言，此类受测者会逃避那些自己认为不能胜任的活动,也更容易感到压力、无力感与沮丧。即使开始了一个任务，他们中有一些人往往会对完成任务的结果过度担心，从而引发焦虑，反而导致任务完成的情况不好。在别人眼中，此类受测者总是不够自信，因为他们“老是低头转移目光”、“不吭声”、“不够主动”等等。',
        46 => '此类受测者在任务和困难面前常常不够果断，因为他们会花大量时间思考自己的决定是否正确，即使经过反复论证他们也还会犹疑不定。如果这些任务和困难是别人要去解决的，他们反而会从旁观者的角度很快地做出判断并给出适当建议。究其原因，主要是因为他们对于自己能否顺利完成任务并没有什么信心。与此同时，由于很在意自己完成任务的结果，此类受测者常会在工作中莫名感到迷茫和焦虑，一旦他们认为工作难度过大，他们会很难坚持下去。在选择工作任务和目标时，他们往往会选择难度一般，甚至是偏简单的工作，因为他们认为自己只能完成这一类的工作。',
        49 => '“力不从心”常常困扰着此类受测者，他们对自我的评估往往低于他们真实的水平，而他们却认识不到这一点，而将其归结为“能力不够”。在外人看来，这也许是一种谦虚的表现，但实际上他们内心真是这样认为。如果他们受到更多的鼓励，他们会表现得比现在更好。也正因为这个缘故，他们在工作中需要带领和激励，外部的力量更能推动他们坚持到底。',
        52 => '此类受测者对自己的能力较有信心，他们清楚自己能够完成什么，不能完成什么。他们选择的常常是适合自己能力水平的任务，并对顺利完成这些任务颇有把握。面对失败，此类受测者多数时候会认为是自己努力不够，或是缺乏某些可以学习的知识或技能。他们很少会有“自己是个失败者”的想法，而总是认为“只要我再努力一些就可以做到”。这样的想法常常帮助他们振作起来，比原来更加努力。因此，此类受测者在工作中很少有沮丧、灰心的感觉，即使事情进行得不顺利，他们也能给团队带来正面、积极的影响。',
    ];

    //分量表分数
    const SUBSCALE_CONTENT = [

    ];


    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $username, string $password)
    {
        $user = $this->adminData->getDetail($username);
        if (empty($user)) {
            Log::error('用户不存在，username=' . $username);
            throw new \Exception('用户不存在');
        }

        $password = md5(md5($user->salt) . $password);
        if ($password != $user->password) {
            Log::error('用户密码不正确，username=' . $username . ',password=' . $password);
            throw new \Exception('用户用户密码不正确');
        }

        [$token, $expires] = $this->setToken($user->id);
        $user->token   = $token;
        $user->expires = $expires;
        $user->save();

        return [
            'uid'      => $user->id,
            'username' => $user->username,
            'token'    => $token,
        ];
    }

    /**
     * @param int    $uid
     * @param string $token
     *
     * @return bool
     */
    public function checkToken(int $uid, string $token): bool
    {
        $tokenArr = $this->getToken($uid);
        if (empty($tokenArr['token']) || $tokenArr['token'] != $token || $tokenArr['expire'] <= time()) {
            Log::error('token无效,uid=' . $uid . ',token=' . $token);
            return false;
//            throw new \Exception('token无效');
        }

        return true;
    }

    /**
     * @param int $uid
     *
     * @return array|mixed
     */
    private function getToken(int $uid)
    {
        $key   = $this->loginInfoKey($uid);
        $token = redis()->get($key);
        if (empty($token)) {
            $user  = $this->adminData->getDetailByUid($uid);
            $token = empty($user) ? [] : $user->toArray();
        }

        return $token;
    }

    private function loginInfoKey($uid)
    {
        return $key = CacheConst::LOGIN_INFO . $uid;
    }

    /**
     * @param $uid
     *
     * @return array
     */
    private function setToken($uid): array
    {
        $expire   = self::$expire;
        $token    = get_uniqid();
        $tokenArr = [
            'token'  => $token,
            'expire' => $expire + time(),
        ];

        $key = $this->loginInfoKey($uid);
        redis()->set($key, $tokenArr, $expire);

        return [$token, $expire + time()];
    }

    /**
     * 退出
     *
     * @param int $uid
     */
    public function loginOut(int $uid): void
    {
        $key = $this->loginInfoKey($uid);
        redis()->expire($key, 0);

        $this->adminData->update($uid, ['token' => '']);
    }

    /**
     * 答案列表
     *
     * @param string $startTime
     * @param string $endTime
     * @param int    $page
     * @param int    $size
     *
     * @return array
     */
    public function answerList(string $startTime, string $endTime, int $page, int $size)
    {
        $conditions = [
            'and',
            ['>=', 'ctime', strtotime($startTime)],
            ['<', 'ctime', strtotime($endTime . ' 23:59:59')],
            ['status' => CommonConst::STATUS_YES]
        ];

        $count    = $this->userData->getCount($conditions);
        $userList = $this->userData->getList($conditions, $page, $size);
        if (empty($userList)) {
            return [
                'page'  => $page,
                'total' => $count,
                'list'  => []
            ];
        }

        $uids       = array_column($userList, 'id');
        $answerList = $this->answerData->getList(['uid' => $uids]);
        $answerList = array_column($answerList, null, 'uid');

        foreach ($userList as &$user) {
            $user   = $user->toArray();
            $answer = empty($answerList[$user['id']]) ? [] : $answerList[$user['id']]->toArray();
            $user   = [
                'id'             => $user['id'],
                'username'       => $user['username'],
                'sex'            => $user['sex'],
                'mobile'         => $user['mobile'],
                'totalPoints'    => $answer['totalPoints'] ?? 0,
                'subscalePoints' => $answer['subscalePoints'] ?? 0,
                'ctime'          => date('Y-m-d H:i:s', $user['ctime']),
            ];
        }

        return [
            'page'  => $page,
            'total' => $count,
            'list'  => $userList
        ];
    }

    const POLITICAL_STATUS = [
        1 => '群众',
        2 => '团员',
        3 => '党员',
    ];

    const MERRY_STATUS = [
        'A' => '已婚',
        'B' => '未婚',
        'C' => '离异',
        'D' => '丧偶'
    ];

    const EDUCTION     = [
        'A' => '初中及以下',
        'B' => '高中/中专',
        'C' => '高职/大专',
        'D' => '大学本科',
        'E' => '硕士（包括MBA,EMBA,MPA等）',
        'F' => '博士',
    ];
    const CHILDREN_NUM = [
        'A' => '1个',
        'B' => '2个',
        'C' => '3个及以上',
    ];

    /**
     * 下载
     *
     * @param string $startTime
     * @param string $endTime
     *
     * @throws \yii\base\ExitException
     */
    public function download(string $startTime, string $endTime)
    {
        $conditions = [
            'and',
            ['>=', 'ctime', strtotime($startTime)],
            ['<', 'ctime', strtotime($endTime . ' 23:59:59')],
            ['status' => CommonConst::STATUS_YES]
        ];
        $userList   = $this->userData->getAllList($conditions);
        if (empty($userList)) {
            Log::warning('下载数据为空，cond=' . json_encode($conditions));
        }

        $uids       = array_column($userList, 'id');
        $answerList = $this->answerData->getList(['uid' => $uids]);
        $answerList = array_column($answerList, null, 'uid');

        foreach ($userList as &$user) {
            $user     = $user->toArray();
            $answer   = empty($answerList[$user['id']]) ? [] : $answerList[$user['id']]->toArray();
            $childNum = ($user['childrenOrNot'] == 'B') ? '无' : (self::CHILDREN_NUM[$user['childrenNum']] ?? '1个');
            $user     = [
                'id'                  => $user['id'],
                'username'            => $user['username'],
                'mobile'              => $user['mobile'],
                'sex'                 => ($user['sex'] == 'A') ? '男' : '女',
                'nation'              => $user['nation'],
                'birthday'            => $user['birthday'],
                'maritalStatus'       => self::MERRY_STATUS[$user['maritalStatus']] ?? '未婚',
                'politicalStatus'     => self::POLITICAL_STATUS[$user['politicalStatus']] ?? '群众',
                'education'           => self::EDUCTION[$user['education']] ?? '初中及以下',
                'childrenOrNot'       => ($user['childrenOrNot'] == 'A') ? '是' : '否',
                'childrenNum'         => $childNum,
                'parentWorkStatus'    => ($user['parentWorkStatus'] == CommonConst::STATUS_YES) ? '是' : '否',
                'subscalePoints'      => (($answer['subscalePoints'] ?? 0) <= 0) ? '建议作废' : ($answer['subscalePoints'] ?? 0),
                'totalPoints'         => $answer['totalPoints'],
                'totalPointsContent'  => $this->getRemarkByPoints('totalPoints', $answer['totalPoints']),
                'frustrationPoints'   => $answer['frustrationPoints'],
                'frustrationContent'  => $this->getRemarkByPoints('frustrationPoints', $answer['frustrationPoints']),
                'responsiblePoints'   => $answer['responsiblePoints'],
                'responsibleContent'  => $this->getRemarkByPoints('responsiblePoints', $answer['responsiblePoints']),
                'debuggingPoints'     => $answer['debuggingPoints'],
                'debuggingContent'    => $this->getRemarkByPoints('debuggingPoints', $answer['debuggingPoints']),
                'assistancePoints'    => $answer['assistancePoints'],
                'assistanceContent'   => $this->getRemarkByPoints('assistancePoints', $answer['assistancePoints']),
                'selfEfficacyPoints'  => $answer['selfEfficacyPoints'],
                'selfEfficacyContent' => $this->getRemarkByPoints('selfEfficacyPoints', $answer['selfEfficacyPoints']),
                'ctime'               => date('Y-m-d H:i:s', $user['ctime']),
            ];
        }

        $answerList = [];
        $fileName   = '问卷列表' . date('Y-m-d H:i:s') . '.csv';

        $headers = [
            'id'                  => '序号',
            'username'            => '用户姓名',
            'mobile'              => '电话',
            'sex'                 => '性别',
            'nation'              => '名族',
            'birthday'            => '生日',
            'maritalStatus'       => '婚姻状态',
            'politicalStatus'     => '政治面貌',
            'education'           => '受教育程度',
            'childrenOrNot'       => '有无子女',
            'childrenNum'         => '子女数量',
            'parentWorkStatus'    => '父母或近亲是否在气象行业工作',
            'subscalePoints'      => '测谎分数',
            'totalPoints'         => '总分',
            'totalPointsContent'  => '总分评语',
            'frustrationPoints'   => '耐挫抗压能力因素评分',
            'frustrationContent'  => '耐挫抗压能力因素评分评语',
            'responsiblePoints'   => '责任心因素评分',
            'responsibleContent'  => '责任心因素评分评语',
            'debuggingPoints'     => '自我调节因素评分',
            'debuggingContent'    => '自我调节因素评分评语',
            'assistancePoints'    => '团队协作因素评分',
            'assistanceContent'   => '团队协作因素评分评语',
            'selfEfficacyPoints'  => '自我效能感因素评分',
            'selfEfficacyContent' => '自我效能感因素评分评语',
            'ctime'               => '提交时间',
        ];

        CsvHelper::exportCsv($answerList, $headers, ['fileName' => $fileName, 'convType' => true]);
    }

    /**
     * 答案分析详情
     *
     * @param int $uid
     *
     * @return array
     * @throws \Exception
     */
    public function answerDetail(int $uid)
    {
        $user   = $this->userData->getDetail($uid);
        $answer = $this->answerData->getDetail($uid);

        if (empty($user) || empty($answer)) {
            Log::error('问卷不存在,uid' . $uid);
            throw new \Exception('问卷不存在');
        }

        $user    = $user->toArray();
        $answer  = empty($answer) ? [] : $answer->toArray();
        $useTime = ($answer['ctime'] ?? 0) - $user['ctime'];
        return [
            'answerDetail' => [
                'id'                  => $uid,
                'username'            => $user['username'],
                'sex'                 => $user['sex'],
                'mobile'              => $user['mobile'],
                'useTime'             => $useTime < 0 ? 0 : $useTime,
                'ctime'               => date('Y-m-d H:i:s', $user['ctime']),
                'totalPoints'         => $answer['totalPoints'],
                'frustrationPoints'   => $answer['frustrationPoints'],
                'responsiblePoints'   => $answer['responsiblePoints'],
                'debuggingPoints'     => $answer['debuggingPoints'],
                'assistancePoints'    => $answer['assistancePoints'],
                'selfEfficacyPoints'  => $answer['selfEfficacyPoints'],
                'subscalePoints'      => $answer['subscalePoints'],
                'totalPointsContent'  => $this->getRemarkByPoints('totalPoints', $answer['totalPoints']),
                'frustrationContent'  => $this->getRemarkByPoints('frustrationPoints', $answer['frustrationPoints']),
                'responsibleContent'  => $this->getRemarkByPoints('responsiblePoints', $answer['responsiblePoints']),
                'debuggingContent'    => $this->getRemarkByPoints('debuggingPoints', $answer['debuggingPoints']),
                'assistanceContent'   => $this->getRemarkByPoints('assistancePoints', $answer['assistancePoints']),
                'selfEfficacyContent' => $this->getRemarkByPoints('selfEfficacyPoints', $answer['selfEfficacyPoints']),
            ]
        ];
    }

    /**
     * 根据分数获取评语
     *
     * @param $key
     * @param $value
     *
     * @return mixed|string
     */
    private function getRemarkByPoints($key, $value)
    {
        switch ($key) {
            case 'totalPoints':
                $content    = self::TOTAL_CONTENT;
                $bestRemark = self::TOTAL_BEST_REMARK;

                break;
            case 'frustrationPoints':
                $content    = self::FRUSTRATION_CONTENT;
                $bestRemark = self::FRUSTRATION_BEST_REMARK;
                break;
            case 'responsiblePoints':
                $content    = self::RESPONSIBLE_CONTENT;
                $bestRemark = self::RESPONSIBLE_BEST_REMARK;
                break;
            case 'debuggingPoints':
                $content    = self::DEBUGGING_CONTENT;
                $bestRemark = self::DEBUGGING_BEST_REMARK;
                break;
            case 'assistancePoints':
                $content    = self::ASSISTANCE_CONTENT;
                $bestRemark = self::ASSISTANCE_BEST_REMARK;
                break;
            case 'selfEfficacyPoints':
                $content    = self::SELF_EFFICACY_CONTENT;
                $bestRemark = self::SELF_EFFICACY_BEST_REMARK;
                break;

            default:
                $content    = [];
                $bestRemark = '';
                break;
        }

        foreach ($content as $point => $item) {
            if ($value <= $point) {
                $remark = $item;
                continue;
            }

            if ($value > $point) {
                break;
            }
        }

        return $remark ?? $bestRemark;
    }
}
