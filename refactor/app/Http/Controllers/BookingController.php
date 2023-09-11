<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Distance;
use DTApi\Models\Job;
use DTApi\Repository\BookingRepository;
use Illuminate\Http\Request;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    // index function entry point of page function starts
    public function index(Request $request)
    {
        try {if ($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobs($user_id);

        } elseif ($request->__authenticatedUser->user_type == config('roles.ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == config('roles.SUPERADMIN_ROLE_ID')) {
            $response = $this->repository->getAll($request);
        }

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    // index function entry point of page function close
    /**
     * @param $id
     * @return mixed
     */
    // manage show funciton starts
    public function show($id)
    {
        try { $job = $this->repository->with('translatorJobRel.user')->find($id);

            return response($job);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    // close
    /**
     * @param Request $request
     * @return mixed
     */
    // store funciotn
    public function store(Request $request)
    {
        try { $data = $request->all();

            $response = $this->repository->store($request->__authenticatedUser, $data);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try { $data = $request->all();
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            $data = $request->all();

            $response = $this->repository->storeJobEmail($data);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try {if ($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response()->json($response)->setStatusCode(200);

        }

            return null;
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try { $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJob($data, $user);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    public function acceptJobWithId(Request $request)
    {
        try {
            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJobWithId($data, $user);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try { $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->cancelJobAjax($data, $user);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try { $data = $request->all();

            $response = $this->repository->endJob($data);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    public function customerNotCall(Request $request)
    {
        try { $data = $request->all();

            $response = $this->repository->customerNotCall($data);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    // function starts
    public function getPotentialJobs(Request $request)
    {
        try {
            $user = $request->__authenticatedUser;

            $response = $this->repository->getPotentialJobs($user);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }

    }
    // funciton ends

    public function distanceFeed(Request $request)
    {
        try {
            $data = $request->all();
            $updateAbleArr = [];

            // get and set values in the $updateAbleArr array
            $updateAbleArr['distance'] = isset($data['distance']) && $data['distance'] !== '' ? $data['distance'] : '';
            $updateAbleArr['time'] = isset($data['time']) && $data['time'] !== '' ? $data['time'] : '';
            $jobid = isset($data['jobid']) && $data['jobid'] !== '' ? $data['jobid'] : '';
            $updateAbleArr['session_time'] = isset($data['session_time']) && $data['session_time'] !== '' ? $data['session_time'] : '';
            $flagged = $data['flagged'] === 'true' ? ($data['admincomment'] !== '' ? 'yes' : 'Please, add comment') : 'no';
            $updateAbleArr['admin_comments'] = isset($data['admincomment']) && $data['admincomment'] !== '' ? $data['admincomment'] : '';
            $updateAbleArr['flagged'] = $flagged;
            $updateAbleArr['manually_handled'] = $data['manually_handled'] === 'true' ? 'yes' : 'no';
            $updateAbleArr['by_admin'] = $data['by_admin'] === 'true' ? 'yes' : 'no';

            $affectedRows = 0;
            // update models
            if ($updateAbleArr['time'] || $updateAbleArr['distance']) {
                $affectedRows += Distance::where('job_id', $jobid)->update($updateAbleArr);
            }

            if ($updateAbleArr['admin_comments'] || $updateAbleArr['session_time'] || $updateAbleArr['flagged'] || $updateAbleArr['manually_handled'] || $updateAbleArr['by_admin']) {
                $affectedRows += Job::where('id', $jobid)->update($updateAbleArr);
            }

            return response(['success' => true, 'message' => 'Record updated!']);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    public function reopen(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->reopen($data);

            return response()->json($response)->setStatusCode(200);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resendNotifications(Request $request)
    {
        try { $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');

            return response(['success' => true, 'message' => 'Push sent']);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    //  funciton for resending sms notificaitons  starts
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    //  funciton for resending sms notificaitons  close

}
