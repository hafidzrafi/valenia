import unittest
from unittest.mock import patch
from datetime import datetime
import sys
import os

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "../.github/scripts")))
import export_logbook


class TestMemberResolver(unittest.TestCase):
    def test_resolve_known_members_with_whitespace(self):
        self.assertEqual(export_logbook.resolve_member_name([{"name": "Raditya "}]), "Raditya")
        self.assertEqual(export_logbook.resolve_member_name([{"name": "FINDI FINANDA ASZAHRA "}]), "Findi")
        self.assertEqual(export_logbook.resolve_member_name([{"name": "Galuh Pramu"}]), "Galuh")
        self.assertEqual(export_logbook.resolve_member_name([{"name": "Hafidz Rafi"}]), "Hafidz")

    def test_resolve_empty_or_unknown_member(self):
        self.assertEqual(export_logbook.resolve_member_name([]), "Unassigned")
        self.assertEqual(export_logbook.resolve_member_name([{"name": "Random Person"}]), "Random Person")

    def test_resolve_member_name_malformed_entries(self):
        self.assertEqual(export_logbook.resolve_member_name([None]), "Unassigned")
        self.assertEqual(export_logbook.resolve_member_name([{"name": None}]), "Unassigned")
        self.assertEqual(export_logbook.resolve_member_name([{"name": 123}]), "Unassigned")
        self.assertEqual(export_logbook.resolve_member_name([{"name": "   "}]), "Unassigned")
        self.assertEqual(export_logbook.resolve_member_name(["invalid-type"]), "Unassigned")


    def test_resolve_multiple_members(self):
        members = [{"name": "Raditya"}, {"name": "Hafidz Rafi"}]
        self.assertEqual(export_logbook.resolve_member_name(members), "Raditya, Hafidz")


class TestEvidenceFormatter(unittest.TestCase):
    def test_github_pr_url(self):
        link, label = export_logbook.format_evidence_link("https://github.com/hafidzrafi/valenia/pull/3")
        self.assertEqual(link, "https://github.com/hafidzrafi/valenia/pull/3")
        self.assertEqual(label, "PR #3")

    def test_github_commit_url(self):
        link, label = export_logbook.format_evidence_link("https://github.com/hafidzrafi/valenia/commit/f7a4892718b7")
        self.assertEqual(label, "Commit f7a4892")

    def test_format_evidence_link_uppercase_commit_sha(self):
        link, label = export_logbook.format_evidence_link("https://github.com/hafidzrafi/valenia/commit/F7A4892718B7")
        self.assertEqual(label, "Commit F7A4892")

    def test_empty_or_generic_url(self):
        self.assertEqual(export_logbook.format_evidence_link("")[1], "-")
        self.assertEqual(export_logbook.format_evidence_link("https://figma.com/file/123")[1], "Figma Design")
        self.assertEqual(export_logbook.format_evidence_link("https://notion.so/doc")[1], "Notion Doc")

    def test_reject_unsafe_uri_schemes(self):
        self.assertEqual(export_logbook.format_evidence_link("javascript:alert(1)"), ("", "-"))
        self.assertEqual(export_logbook.format_evidence_link("file:///etc/passwd"), ("", "-"))
        self.assertEqual(export_logbook.format_evidence_link("data:text/html,test"), ("", "-"))


class TestHoursEstimator(unittest.TestCase):
    def test_priority_fallbacks(self):
        self.assertEqual(export_logbook.estimate_hours("Must", None), 4)
        self.assertEqual(export_logbook.estimate_hours("Should", None), 3)
        self.assertEqual(export_logbook.estimate_hours("Could", None), 2)
        self.assertEqual(export_logbook.estimate_hours(None, None), 2)

    def test_notes_override(self):
        self.assertEqual(export_logbook.estimate_hours("Must", "Refactoring auth [hours: 6]"), 6)

    def test_tier_priority_fallbacks(self):
        self.assertEqual(export_logbook.estimate_hours("Tier 1 🔥‼", None), 4)
        self.assertEqual(export_logbook.estimate_hours("Tier 2 ‼", None), 3)
        self.assertEqual(export_logbook.estimate_hours("Tier 3", None), 2)

    def test_explicit_hours_override(self):
        self.assertEqual(export_logbook.estimate_hours("Tier 1 🔥‼", "Notes [hours: 6]", explicit_hours=8), 8)
        self.assertEqual(export_logbook.estimate_hours("Tier 3", None, explicit_hours=1), 1)
        self.assertEqual(export_logbook.estimate_hours("Tier 1 🔥‼", None, explicit_hours=0), 1)
        self.assertEqual(export_logbook.estimate_hours("Tier 1 🔥‼", None, explicit_hours=-3), 1)

    def test_estimate_hours_clamps_zero_or_negative(self):
        # [hours: 0] or negative hours should strictly clamp to 1 hour
        self.assertEqual(export_logbook.estimate_hours("Must", "Quick fix [hours: 0]"), 1)
        self.assertEqual(export_logbook.estimate_hours("Could", "Quick fix [hours: -2]"), 1)


class TestDateFormatter(unittest.TestCase):
    def test_iso_date_format(self):
        self.assertEqual(export_logbook.format_date("2026-09-24T12:00:00.000Z"), "24 Sep 2026")
        self.assertEqual(export_logbook.format_date("2026-09-20"), "20 Sep 2026")

    def test_empty_date(self):
        self.assertEqual(export_logbook.format_date(None), "-")

    def test_whitespace_and_invalid_date(self):
        self.assertEqual(export_logbook.format_date("   "), "-")
        self.assertEqual(export_logbook.format_date(123), "-")


class TestTaskNormalization(unittest.TestCase):
    def test_normalize_complete_task(self):
        page = {
            "last_edited_time": "2026-09-24T10:00:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 12}},
                "Task Name": {"title": [{"plain_text": "Automate Logbook"}]},
                "Person": {"people": [{"name": "Hafidz Rafi"}]},
                "PR / Commit Link": {"url": "https://github.com/hafidzrafi/valenia/pull/4"},
                "Notes": {"rich_text": [{"plain_text": "Script export_logbook.py teruji"}]},
                "Deadline": {"date": {"start": "2026-09-25"}},
                "Priority": {"select": {"name": "Must"}},
            }
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["task"], "VALENIA-12: Automate Logbook")
        self.assertEqual(activity["member"], "Hafidz")
        self.assertEqual(activity["date"], "25 Sep 2026")
        self.assertEqual(activity["deliverable"], "Script export_logbook.py teruji")
        self.assertEqual(activity["link"], "https://github.com/hafidzrafi/valenia/pull/4")
        self.assertEqual(activity["evidence_label"], "PR #4")
        self.assertEqual(activity["hours"], 4)

    def test_normalize_task_with_est_hours_select(self):
        page = {
            "last_edited_time": "2026-09-24T10:00:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 15}},
                "Task Name": {"title": [{"plain_text": "add gitkeep"}]},
                "Person": {"people": [{"name": "Findi"}]},
                "Priority": {"select": {"name": "Tier 2 ‼"}},
                "Est. Hours": {"type": "select", "select": {"name": "1"}},
            },
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["hours"], 1)

    def test_normalize_task_with_est_hours_number(self):
        page = {
            "last_edited_time": "2026-09-24T10:00:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 10}},
                "Task Name": {"title": [{"plain_text": "Setup Notion"}]},
                "Person": {"people": [{"name": "Hafidz"}]},
                "Priority": {"select": {"name": "Tier 1 🔥‼"}},
                "Est. Hours": {"type": "number", "number": 6},
            },
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["hours"], 6)

    def test_normalize_task_with_est_hours_select_whitespace(self):
        page = {
            "properties": {
                "Priority": {"select": {"name": "Tier 1 🔥‼"}},
                "Est. Hours": {"type": "select", "select": {"name": "  5  "}},
            }
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["hours"], 5)

    def test_normalize_task_with_est_hours_select_invalid_string_falls_back_to_tier(self):
        page = {
            "properties": {
                "Priority": {"select": {"name": "Tier 2 ‼"}},
                "Est. Hours": {"type": "select", "select": {"name": "invalid-num"}},
            }
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["hours"], 3)

    def test_normalize_task_resolves_completion_date_when_deadline_is_future(self):
        page = {
            "last_edited_time": "2026-09-26T11:51:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 5}},
                "Task Name": {"title": [{"plain_text": "Design System"}]},
                "Status": {"status": {"name": "Done"}},
                "Deadline": {"date": {"start": "2026-10-01"}},
            },
        }
        w_start = datetime(2026, 9, 21)
        w_end = datetime(2026, 9, 27, 23, 59, 59)
        activity = export_logbook.normalize_task_to_activity(page, week_start=w_start, week_end=w_end)
        self.assertEqual(activity["date"], "26 Sep 2026", "Future deadline must fall back to completion date")

    def test_normalize_task_keeps_deadline_when_inside_window(self):
        page = {
            "last_edited_time": "2026-09-26T11:51:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 5}},
                "Task Name": {"title": [{"plain_text": "Design System"}]},
                "Status": {"status": {"name": "Done"}},
                "Deadline": {"date": {"start": "2026-09-24"}},
            },
        }
        w_start = datetime(2026, 9, 21)
        w_end = datetime(2026, 9, 27, 23, 59, 59)
        activity = export_logbook.normalize_task_to_activity(page, week_start=w_start, week_end=w_end)
        self.assertEqual(activity["date"], "24 Sep 2026", "Valid deadline inside window must be preserved")



class TestNotionExtractor(unittest.TestCase):
    def test_extract_sprint_number_from_title(self):
        self.assertEqual(export_logbook.extract_sprint_number_from_title("Sprint 1: Core Infrastructure"), 1)
        self.assertEqual(export_logbook.extract_sprint_number_from_title("Sprint 04 - Checkpoint 2"), 4)
        self.assertEqual(export_logbook.extract_sprint_number_from_title("Sprint 12"), 12)
        self.assertIsNone(export_logbook.extract_sprint_number_from_title("Backlog Exploration"))

    @patch("export_logbook.call_notion_api")
    def test_fetch_active_sprint_finds_active_status(self, mock_api):
        mock_api.return_value = {
            "results": [
                {
                    "id": "sprint-active-id",
                    "properties": {
                        "Sprint Name": {"title": [{"plain_text": "Sprint 2: Architecture"}]},
                        "Status": {"status": {"name": "Active"}},
                    },
                }
            ],
            "has_more": False,
        }
        sprint, sprint_num = export_logbook.fetch_active_sprint("sprints-db", "token")
        self.assertIsNotNone(sprint)
        self.assertEqual(sprint["id"], "sprint-active-id")
        self.assertEqual(sprint_num, 2)

    @patch("export_logbook.call_notion_api")
    def test_fetch_active_sprint_fallback_when_no_active(self, mock_api):
        mock_api.side_effect = [
            {"results": [], "has_more": False},
            {
                "results": [
                    {
                        "id": "sprint-fallback-id",
                        "properties": {
                            "Sprint Name": {"title": [{"plain_text": "Sprint 1: Core"}]},
                            "Status": {"status": {"name": "Planned"}},
                        },
                    }
                ],
                "has_more": False,
            },
        ]
        sprint, sprint_num = export_logbook.fetch_active_sprint("sprints-db", "token")
        self.assertIsNotNone(sprint)
        self.assertEqual(sprint["id"], "sprint-fallback-id")
        self.assertEqual(sprint_num, 1)

    @patch("export_logbook.call_notion_api")
    def test_fetch_tasks_handles_pagination(self, mock_api):
        mock_api.side_effect = [
            {"results": [{"id": "page-1"}], "has_more": True, "next_cursor": "cur-1"},
            {"results": [{"id": "page-2"}], "has_more": False, "next_cursor": None},
        ]
        tasks = export_logbook.fetch_tasks_for_sprint("tasks-db", "fake-token", "sprint-id", only_done=False)
        self.assertEqual(len(tasks), 2)
        self.assertEqual(mock_api.call_count, 2)
        # Verify second call used start_cursor
        second_call_payload = mock_api.call_args_list[1][1]["payload"]
        self.assertEqual(second_call_payload.get("start_cursor"), "cur-1")

    @patch("export_logbook.call_notion_api")
    def test_fetch_sprint_by_number(self, mock_api):
        mock_api.return_value = {
            "results": [
                {
                    "id": "sprint-1-id",
                    "properties": {
                        "Sprint Name": {"title": [{"plain_text": "Sprint 1: Core Setup"}]},
                        "Status": {"status": {"name": "Active"}},
                    },
                },
                {
                    "id": "sprint-2-id",
                    "properties": {
                        "Sprint Name": {"title": [{"plain_text": "Sprint 2: UI Design"}]},
                        "Status": {"status": {"name": "Planned"}},
                    },
                },
            ]
        }
        sprint = export_logbook.fetch_sprint_by_number("sprints-db", "fake-token", 1)
        self.assertIsNotNone(sprint)
        self.assertEqual(sprint["id"], "sprint-1-id")

        sprint_none = export_logbook.fetch_sprint_by_number("sprints-db", "fake-token", 99)
        self.assertIsNone(sprint_none)

    @patch("export_logbook.call_notion_api")
    def test_fetch_sprint_with_none_properties_does_not_crash(self, mock_api):
        mock_api.return_value = {
            "results": [
                {"id": "sprint-null-props", "properties": None},
                {"id": "sprint-null-status", "properties": {"Status": None}},
                {
                    "id": "sprint-active-id",
                    "properties": {
                        "Sprint Name": {"title": [{"plain_text": "Sprint 2"}]},
                        "Status": {"status": {"name": "Active"}},
                    },
                },
            ]
        }
        sprint = export_logbook.fetch_sprint_by_number("sprints-db", "token", 2)
        self.assertIsNotNone(sprint)
        self.assertEqual(sprint["id"], "sprint-active-id")

    @patch("export_logbook.call_notion_api")
    def test_fetch_sprint_handles_pagination(self, mock_api):
        mock_api.side_effect = [
            {"results": [{"id": "sp-1", "properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}], "has_more": True, "next_cursor": "c-1"},
            {"results": [{"id": "sp-2", "properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 2"}]}}}], "has_more": False, "next_cursor": None},
        ]
        sprint = export_logbook.fetch_sprint_by_number("sprints-db", "token", 2)
        self.assertIsNotNone(sprint)
        self.assertEqual(sprint["id"], "sp-2")
        self.assertEqual(mock_api.call_count, 2)
        self.assertEqual(mock_api.call_args_list[1][1]["payload"].get("start_cursor"), "c-1")


class TestAcademicCalendarCalculator(unittest.TestCase):
    def test_calculate_academic_week_from_sprint_number_two_week_cadence(self):
        # 2-week sprint cadence: Sprint 1 (W5-6), Sprint 2 (W7-8), Sprint 3 (W9-10), Sprint 4 (W11-12)
        # When reference date is outside sprint window or default, it defaults to sprint start week
        ref_outside = datetime(2026, 8, 25)  # Week 1
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=1), 5)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=2), 7)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=3), 9)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=4), 11)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=5), 13)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=ref_outside, sprint_number=6), 15)

    def test_calculate_academic_week_resolves_exact_week_within_two_week_sprint(self):
        # Sprint 1 covers Week 5 and Week 6
        w5_date = datetime(2026, 9, 27)  # Sunday Week 5
        w6_date = datetime(2026, 10, 4)  # Sunday Week 6
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=w5_date, sprint_number=1), 5)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=w6_date, sprint_number=1), 6)

        # Sprint 2 covers Week 7 and Week 8
        w7_date = datetime(2026, 10, 11)  # Sunday Week 7
        w8_date = datetime(2026, 10, 18)  # Sunday Week 8
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=w7_date, sprint_number=2), 7)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=w8_date, sprint_number=2), 8)

    def test_calculate_academic_week_from_date(self):
        d = datetime(2026, 9, 15)
        self.assertEqual(export_logbook.calculate_academic_week(reference_date=d), 4)

    def test_derive_checkpoint_target(self):
        self.assertEqual(export_logbook.derive_checkpoint_target(1), "Checkpoint 1 (Minggu ke-4)")
        self.assertEqual(export_logbook.derive_checkpoint_target(4), "Checkpoint 1 (Minggu ke-4)")
        self.assertEqual(export_logbook.derive_checkpoint_target(5), "Checkpoint 2 (Minggu ke-8)")
        self.assertEqual(export_logbook.derive_checkpoint_target(8), "Checkpoint 2 (Minggu ke-8)")
        self.assertEqual(export_logbook.derive_checkpoint_target(9), "Checkpoint 3 (Minggu ke-12)")
        self.assertEqual(export_logbook.derive_checkpoint_target(12), "Checkpoint 3 (Minggu ke-12)")
        self.assertEqual(export_logbook.derive_checkpoint_target(13), "Checkpoint 4 (Minggu ke-16)")
        self.assertEqual(export_logbook.derive_checkpoint_target(16), "Checkpoint 4 (Minggu ke-16)")



class TestPayloadBuilder(unittest.TestCase):
    def test_build_sprint_payload(self):
        sprint = {
            "properties": {
                "Sprint Name": {"title": [{"plain_text": "Sprint 1: Core Infra"}]},
                "Dates": {"date": {"start": "2026-09-18", "end": "2026-09-25"}},
            }
        }
        task = {
            "properties": {
                "ID": {"unique_id": {"number": 1}},
                "Task Name": {"title": [{"plain_text": "Setup Repo"}]},
                "Assignee": {"people": [{"name": "Raditya"}]},
                "PR / Commit Link": {"url": "https://github.com/hafidzrafi/valenia/pull/1"},
                "Notes": {"rich_text": []},
                "Deadline": {"date": {"start": "2026-09-20"}},
                "Priority": {"select": {"name": "Must"}},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [task], week_number=5, strict_week=False)
        self.assertEqual(payload["sprint_name"], "Sprint 1: Core Infra")
        self.assertEqual(payload["week_number"], 5)
        self.assertEqual(payload["period"], "18 Sep 2026 - 25 Sep 2026")
        self.assertEqual(len(payload["activities"]), 1)
        self.assertEqual(payload["activities"][0]["member"], "Raditya")

    def test_build_sprint_payload_date_range_start_only(self):
        sprint = {
            "properties": {
                "Sprint Name": {"title": [{"plain_text": "Sprint 3: Services"}]},
                "Dates": {"date": {"start": "2026-10-01", "end": None}},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [], week_number=7, strict_week=False)
        self.assertIn("1 Okt 2026", payload["period"])
        self.assertNotIn("18 September", payload["period"])

    def test_build_sprint_payload_custom_checkpoint(self):
        sprint = {
            "properties": {
                "Sprint Name": {"title": [{"plain_text": "Sprint 4"}]},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [], week_number=9, checkpoint_target="Checkpoint 3 (Minggu ke-12)")
        self.assertEqual(payload["checkpoint_target"], "Checkpoint 3 (Minggu ke-12)")

    def test_build_sprint_payload_dynamic_period_fallback_when_dates_empty(self):
        sprint = {
            "properties": {
                "Sprint Name": {"title": [{"plain_text": "Sprint 1: Core Infra"}]},
                "Dates": {"date": None},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [], week_number=5)
        # Week 5: Monday 2026-09-21 to Sunday 2026-09-27
        self.assertEqual(payload["period"], "21 Sep 2026 - 27 Sep 2026")

    def test_build_sprint_payload_resolves_future_deadline_tasks_in_activities(self):
        sprint = {
            "properties": {
                "Sprint Name": {"title": [{"plain_text": "Sprint 1: Core Infra"}]},
                "Dates": {"date": {"start": "2026-09-21", "end": "2026-09-27"}},
            }
        }
        task = {
            "last_edited_time": "2026-09-26T11:51:00.000Z",
            "properties": {
                "ID": {"unique_id": {"number": 5}},
                "Task Name": {"title": [{"plain_text": "Design System"}]},
                "Status": {"status": {"name": "Done"}},
                "Deadline": {"date": {"start": "2026-10-01"}},
                "Assignee": {"people": [{"name": "Galuh"}]},
            },
        }
        payload = export_logbook.build_sprint_payload(sprint, [task], week_number=5, strict_week=False)
        self.assertEqual(payload["activities"][0]["date"], "26 Sep 2026")

    def test_build_sprint_payload_strict_week_filters_tasks_outside_window(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        # Week 5 window: 2026-09-21 to 2026-09-27
        task_in_week = {
            "properties": {
                "ID": {"unique_id": {"number": 14}},
                "Task Name": {"title": [{"plain_text": "Skeleton"}]},
                "Deadline": {"date": {"start": "2026-09-26"}},
            }
        }
        task_in_october = {
            "properties": {
                "ID": {"unique_id": {"number": 17}},
                "Task Name": {"title": [{"plain_text": "Frontend Init"}]},
                "Deadline": {"date": {"start": "2026-09-29"}},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [task_in_week, task_in_october], week_number=5, strict_week=True)
        self.assertEqual(len(payload["activities"]), 1)
        self.assertEqual(payload["activities"][0]["task"], "VALENIA-14: Skeleton")
        self.assertEqual(payload["period"], "21 Sep 2026 - 27 Sep 2026")

    def test_build_sprint_payload_week_6_includes_october_tasks(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        # Week 6 window: 2026-09-28 to 2026-10-04
        task_in_week5 = {
            "properties": {
                "ID": {"unique_id": {"number": 14}},
                "Task Name": {"title": [{"plain_text": "Skeleton"}]},
                "Deadline": {"date": {"start": "2026-09-26"}},
            }
        }
        task_in_week6 = {
            "properties": {
                "ID": {"unique_id": {"number": 17}},
                "Task Name": {"title": [{"plain_text": "Frontend Init"}]},
                "Deadline": {"date": {"start": "2026-09-29"}},
            }
        }
        payload = export_logbook.build_sprint_payload(sprint, [task_in_week5, task_in_week6], week_number=6, strict_week=True)
        self.assertEqual(len(payload["activities"]), 1)
        self.assertEqual(payload["activities"][0]["task"], "VALENIA-17: Frontend Init")
        self.assertEqual(payload["period"], "28 Sep 2026 - 4 Okt 2026")

    def test_build_sprint_payload_sorts_activities_chronologically(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        # Week 6: 2026-09-28 to 2026-10-04
        task_oct_4 = {
            "properties": {
                "ID": {"unique_id": {"number": 23}},
                "Task Name": {"title": [{"plain_text": "Refactor Architecture"}]},
                "Deadline": {"date": {"start": "2026-10-04"}},
            }
        }
        task_oct_2 = {
            "properties": {
                "ID": {"unique_id": {"number": 18}},
                "Task Name": {"title": [{"plain_text": "Setup Authentication"}]},
                "Deadline": {"date": {"start": "2026-10-02"}},
            }
        }
        task_sep_29_b = {
            "properties": {
                "ID": {"unique_id": {"number": 17}},
                "Task Name": {"title": [{"plain_text": "Frontend Init"}]},
                "Deadline": {"date": {"start": "2026-09-29"}},
            }
        }
        task_sep_29_a = {
            "properties": {
                "ID": {"unique_id": {"number": 16}},
                "Task Name": {"title": [{"plain_text": "Component Library"}]},
                "Deadline": {"date": {"start": "2026-09-29"}},
            }
        }
        # Pass tasks in random/reverse order
        payload = export_logbook.build_sprint_payload(
            sprint,
            [task_oct_4, task_oct_2, task_sep_29_b, task_sep_29_a],
            week_number=6,
            strict_week=True,
        )
        activity_tasks = [act["task"] for act in payload["activities"]]
        activity_dates = [act["raw_date"] for act in payload["activities"]]

        self.assertEqual(
            activity_tasks,
            [
                "VALENIA-16: Component Library",
                "VALENIA-17: Frontend Init",
                "VALENIA-18: Setup Authentication",
                "VALENIA-23: Refactor Architecture",
            ],
        )
        self.assertEqual(
            activity_dates,
            ["2026-09-29", "2026-09-29", "2026-10-02", "2026-10-04"],
        )

    def test_build_sprint_payload_sorts_decisions_chronologically(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        # Week 5: 2026-09-21 to 2026-09-27
        dec_sep_26 = {
            "properties": {
                "Keputusan": {"title": [{"plain_text": "Adopsi Typst"}]},
                "Date": {"date": {"start": "2026-09-26"}},
            }
        }
        dec_sep_21 = {
            "properties": {
                "Keputusan": {"title": [{"plain_text": "Pemilihan Stack PHP Native"}]},
                "Date": {"date": {"start": "2026-09-21"}},
            }
        }
        dec_sep_23 = {
            "properties": {
                "Keputusan": {"title": [{"plain_text": "Struktur Database PostgreSQL"}]},
                "Date": {"date": {"start": "2026-09-23"}},
            }
        }
        payload = export_logbook.build_sprint_payload(
            sprint,
            [],
            week_number=5,
            decisions=[dec_sep_26, dec_sep_21, dec_sep_23],
            strict_week=True,
        )
        decision_titles = [d["decision"] for d in payload["decisions"]]
        decision_dates = [d["raw_date"] for d in payload["decisions"]]

        self.assertEqual(
            decision_titles,
            [
                "Pemilihan Stack PHP Native",
                "Struktur Database PostgreSQL",
                "Adopsi Typst",
            ],
        )
        self.assertEqual(
            decision_dates,
            ["2026-09-21", "2026-09-23", "2026-09-26"],
        )

    def test_build_sprint_payload_sorts_safely_when_dates_are_none_or_missing(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        task_with_date = {
            "properties": {
                "ID": {"unique_id": {"number": 1}},
                "Task Name": {"title": [{"plain_text": "Task With Date"}]},
                "Deadline": {"date": {"start": "2026-09-22"}},
            }
        }
        task_without_date = {
            "properties": {
                "ID": {"unique_id": {"number": 2}},
                "Task Name": {"title": [{"plain_text": "Task No Date"}]},
            }
        }
        # In non-strict mode, both are kept
        payload = export_logbook.build_sprint_payload(
            sprint,
            [task_with_date, task_without_date],
            week_number=5,
            strict_week=False,
        )
        self.assertEqual(len(payload["activities"]), 2)


class TestLogbookFileGenerator(unittest.TestCase):
    def test_generate_logbook_files(self):
        import tempfile
        import json
        from pathlib import Path

        payload = {
            "week_number": 5,
            "period": "18 Sep 2026 - 25 Sep 2026",
            "sprint_name": "Sprint 1",
            "checkpoint_target": "Checkpoint 2",
            "activities": [],
            "evaluations": [],
        }
        with tempfile.TemporaryDirectory() as tmp_dir:
            json_path, typ_path = export_logbook.generate_logbook_files(payload, tmp_dir)
            self.assertTrue(Path(json_path).exists())
            self.assertTrue(Path(typ_path).exists())

            with open(json_path, "r", encoding="utf-8") as f:
                loaded = json.load(f)
            self.assertEqual(loaded["week_number"], 5)

            with open(typ_path, "r", encoding="utf-8") as f:
                typ_code = f.read()
            self.assertIn("pbl_logbook", typ_code)
            self.assertIn("data.json", typ_code)


class TestTypstCompilerRunner(unittest.TestCase):
    @patch("subprocess.run")
    def test_compile_typst_success(self, mock_run):
        mock_run.return_value.returncode = 0
        success = export_logbook.compile_typst("logbook/sprint-01/main.typ", "logbook/sprint-01/output.pdf")
        self.assertTrue(success)
        mock_run.assert_called_once()
        args = mock_run.call_args[0][0]
        self.assertEqual(args[0], "typst")
        self.assertEqual(args[1], "compile")
        self.assertIn("--root", args)

    @patch("subprocess.run")
    def test_compile_typst_failure(self, mock_run):
        mock_run.return_value.returncode = 1
        mock_run.return_value.stderr = "Typst syntax error"
        success = export_logbook.compile_typst("logbook/sprint-01/main.typ", "logbook/sprint-01/output.pdf")
        self.assertFalse(success)

    @patch("subprocess.run", side_effect=FileNotFoundError("typst not found"))
    def test_compile_typst_not_found(self, mock_run):
        success = export_logbook.compile_typst("logbook/sprint-01/main.typ", "logbook/sprint-01/output.pdf")
        self.assertFalse(success)

    @patch("subprocess.run")
    def test_compile_typst_timeout(self, mock_run):
        import subprocess
        mock_run.side_effect = subprocess.TimeoutExpired(cmd=["typst"], timeout=60)
        success = export_logbook.compile_typst("logbook/sprint-01/main.typ", "logbook/sprint-01/output.pdf")
        self.assertFalse(success)


DEFAULT_CLI_ENV = {
    "NOTION_TOKEN": "token-xyz",
    "NOTION_TASKS_DB_ID": "tasks-db-123",
    "NOTION_SPRINTS_DB_ID": "sprints-db-456",
}


class TestCliMain(unittest.TestCase):
    @patch.dict(os.environ, {}, clear=True)
    def test_main_missing_token_returns_1(self):
        with patch("sys.argv", ["export_logbook.py"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, {"NOTION_TOKEN": "token-xyz"}, clear=True)
    def test_main_missing_tasks_db_returns_1(self):
        with patch("sys.argv", ["export_logbook.py"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, {"NOTION_TOKEN": "token-xyz", "NOTION_TASKS_DB_ID": "db-1"}, clear=True)
    def test_main_missing_sprints_db_returns_1(self):
        with patch("sys.argv", ["export_logbook.py"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    @patch("export_logbook.fetch_sprint_by_number")
    @patch("export_logbook.fetch_tasks_for_sprint")
    @patch("export_logbook.generate_logbook_files")
    @patch("export_logbook.compile_typst")
    def test_main_success_invokes_pipeline(self, mock_compile, mock_gen, mock_tasks, mock_sprint):
        mock_sprint.return_value = {"id": "sprint-1-id", "properties": {}}
        mock_tasks.return_value = []
        mock_gen.return_value = ("logbook/sprint-01/data.json", "logbook/sprint-01/main.typ")
        mock_compile.return_value = True

        with patch("sys.argv", ["export_logbook.py", "--sprint", "1", "--week", "5"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 0)
            mock_sprint.assert_called_once()
            mock_tasks.assert_called_once()
            mock_gen.assert_called_once()
            mock_compile.assert_called_once()

    @patch.dict(os.environ, {
        "NOTION_API_KEY": "token-from-api-key",
        "NOTION_TASKS_DB_ID": "tasks-db",
        "NOTION_SPRINTS_DB_ID": "sprints-db",
    }, clear=True)
    @patch("export_logbook.fetch_sprint_by_number", return_value={"id": "s1", "properties": {}})
    @patch("export_logbook.fetch_tasks_for_sprint", return_value=[])
    @patch("export_logbook.generate_logbook_files", return_value=("data.json", "main.typ"))
    @patch("export_logbook.compile_typst", return_value=True)
    def test_main_supports_notion_api_key_env_var(self, mock_compile, mock_gen, mock_tasks, mock_sprint):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "1"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 0)

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    @patch("export_logbook.fetch_sprint_by_number", return_value={"id": "s1", "properties": {}})
    @patch("export_logbook.fetch_tasks_for_sprint", return_value=[])
    @patch("export_logbook.generate_logbook_files", return_value=("data.json", "main.typ"))
    @patch("export_logbook.compile_typst", return_value=False)
    def test_main_export_logbook_returns_1_when_typst_fails(self, mock_compile, mock_gen, mock_tasks, mock_sprint):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "1"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1, "main() must return 1 when Typst compilation fails")

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    def test_main_rejects_non_positive_sprint_or_week(self):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "0"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

        with patch("sys.argv", ["export_logbook.py", "--sprint", "-2"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

        with patch("sys.argv", ["export_logbook.py", "--sprint", "1", "--week", "0"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    @patch("export_logbook.fetch_sprint_by_number")
    def test_main_handles_api_http_error_gracefully(self, mock_fetch_sprint):
        import io
        from urllib.error import HTTPError
        mock_fetch_sprint.side_effect = HTTPError("url", 401, "Unauthorized", {}, io.BytesIO(b"{}"))

        with patch("sys.argv", ["export_logbook.py", "--sprint", "1"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    @patch("export_logbook.fetch_sprint_by_number", return_value={"id": "s1", "properties": {}})
    @patch("export_logbook.fetch_tasks_for_sprint", return_value=[])
    @patch("export_logbook.generate_logbook_files", return_value=("data.json", "main.typ"))
    @patch("export_logbook.compile_typst", return_value=True)
    def test_main_supports_custom_checkpoint(self, mock_compile, mock_gen, mock_tasks, mock_sprint):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "1", "--checkpoint", "Checkpoint 3 (Minggu ke-12)"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 0)

    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    @patch("export_logbook.fetch_active_sprint")
    @patch("export_logbook.fetch_tasks_for_sprint", return_value=[])
    @patch("export_logbook.generate_logbook_files", return_value=("data.json", "main.typ"))
    @patch("export_logbook.compile_typst", return_value=True)
    def test_main_auto_resolves_active_sprint_and_week(self, mock_compile, mock_gen, mock_tasks, mock_active):
        mock_active.return_value = ({"id": "active-sprint-id", "properties": {}}, 1)
        with patch("sys.argv", ["export_logbook.py"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 0)
            mock_active.assert_called_once()
            mock_tasks.assert_called_once()
            mock_gen.assert_called_once()
            mock_compile.assert_called_once()


class TestTaskNormalizationRobustness(unittest.TestCase):
    def test_normalize_task_with_none_properties_does_not_crash(self):
        page = {
            "id": "page-none-props",
            "properties": {
                "ID": None,
                "Task Name": None,
                "Assignee": None,
                "Person": None,
                "PR / Commit Link": None,
                "Notes": None,
                "Deadline": None,
                "Priority": None,
            },
        }
        activity = export_logbook.normalize_task_to_activity(page)
        self.assertEqual(activity["task"], "VALENIA-XX: ")
        self.assertEqual(activity["member"], "Unassigned")
        self.assertEqual(activity["date"], "-")
        self.assertEqual(activity["hours"], 2)


class TestGovernanceDecisions(unittest.TestCase):
    def test_normalize_decision_to_entry_extracts_correct_fields(self):
        page = {
            "properties": {
                "Keputusan": {"title": [{"plain_text": "Penetapan Struktur Peran Tim PBL"}]},
                "Date": {"date": {"start": "2026-09-21"}},
                "Person": {"people": [{"name": "Raditya "}, {"name": "Hafidz Rafi"}]},
                "Text": {"rich_text": [{"plain_text": "Menyelaraskan pembagian peran teknis"}]},
            }
        }
        entry = export_logbook.normalize_decision_to_entry(page)
        self.assertEqual(entry["decision"], "Penetapan Struktur Peran Tim PBL")
        self.assertEqual(entry["date"], "21 Sep 2026")
        self.assertEqual(entry["decision_maker"], "Raditya, Hafidz")
        self.assertEqual(entry["rationale"], "Menyelaraskan pembagian peran teknis")

    def test_normalize_decision_with_alasan_and_tanggal_properties(self):
        page = {
            "properties": {
                "Title": {"title": [{"plain_text": "Pemilihan Stack"}]},
                "Tanggal": {"date": {"start": "2026-09-22"}},
                "Pengambil Keputusan": {"people": [{"name": "Galuh Pramu"}]},
                "Alasan": {"rich_text": [{"plain_text": "Sesuai PRD v0.3.2"}]},
            }
        }
        entry = export_logbook.normalize_decision_to_entry(page)
        self.assertEqual(entry["decision"], "Pemilihan Stack")
        self.assertEqual(entry["date"], "22 Sep 2026")
        self.assertEqual(entry["decision_maker"], "Galuh")
        self.assertEqual(entry["rationale"], "Sesuai PRD v0.3.2")

    def test_normalize_decision_robustness_with_empty_or_none(self):
        page = {
            "properties": {
                "Keputusan": None,
                "Date": None,
                "Person": None,
                "Alasan": None,
            }
        }
        entry = export_logbook.normalize_decision_to_entry(page)
        self.assertEqual(entry["decision"], "-")
        self.assertEqual(entry["date"], "-")
        self.assertEqual(entry["decision_maker"], "Unassigned")
        self.assertEqual(entry["rationale"], "-")

    def test_fetch_governance_decisions_handles_empty_db_id(self):
        self.assertEqual(export_logbook.fetch_governance_decisions(None, "token"), [])
        self.assertEqual(export_logbook.fetch_governance_decisions("", "token"), [])

    @patch("export_logbook.call_notion_api")
    def test_fetch_governance_decisions_queries_notion_api(self, mock_api):
        mock_api.return_value = {
            "results": [
                {
                    "properties": {
                        "Keputusan": {"title": [{"plain_text": "Decision 1"}]},
                        "Date": {"date": {"start": "2026-09-21"}},
                    }
                }
            ],
            "has_more": False,
        }
        decisions = export_logbook.fetch_governance_decisions("gov-db-id", "token")
        self.assertEqual(len(decisions), 1)
        mock_api.assert_called_once()
        endpoint, token_arg = mock_api.call_args[0][:2]
        self.assertEqual(endpoint, "/databases/gov-db-id/query")

    @patch("export_logbook.call_notion_api")
    def test_fetch_governance_decisions_handles_http_error_gracefully(self, mock_api):
        import io
        from urllib.error import HTTPError
        mock_api.side_effect = HTTPError("url", 404, "Not Found", {}, io.BytesIO(b"{}"))
        decisions = export_logbook.fetch_governance_decisions("invalid-db", "token")
        self.assertEqual(decisions, [])

    def test_build_sprint_payload_includes_decisions(self):
        sprint = {"properties": {"Sprint Name": {"title": [{"plain_text": "Sprint 1"}]}}}
        decisions = [
            {
                "properties": {
                    "Keputusan": {"title": [{"plain_text": "Keputusan 1"}]},
                    "Date": {"date": {"start": "2026-09-21"}},
                    "Person": {"people": [{"name": "Raditya"}]},
                    "Alasan": {"rich_text": [{"plain_text": "Alasan 1"}]},
                }
            }
        ]
        payload = export_logbook.build_sprint_payload(sprint, [], week_number=5, decisions=decisions)
        self.assertIn("decisions", payload)
        self.assertEqual(len(payload["decisions"]), 1)
        self.assertEqual(payload["decisions"][0]["decision"], "Keputusan 1")


class TestNotionFileUpload(unittest.TestCase):
    @patch("urllib.request.urlopen")
    def test_create_notion_file_upload_success(self, mock_urlopen):
        import io
        fake_response = io.BytesIO(b'{"id": "upload-123", "upload_url": "https://upload.notion.com/xyz"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        upload_id, upload_url = export_logbook.create_notion_file_upload("test.pdf", "fake-token")
        self.assertEqual(upload_id, "upload-123")
        self.assertEqual(upload_url, "https://upload.notion.com/xyz")

    @patch("urllib.request.urlopen")
    def test_send_notion_file_bytes_success(self, mock_urlopen):
        import tempfile
        import io
        fake_response = io.BytesIO(b'{"status": "ok"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 dummy")
            tmp.flush()
            ok = export_logbook.send_notion_file_bytes("https://upload.notion.com/xyz", tmp.name, "token")
            self.assertTrue(ok)

    @patch("urllib.request.urlopen")
    def test_attach_file_to_notion_page_success(self, mock_urlopen):
        import io
        fake_response = io.BytesIO(b'{"id": "page-123"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        ok = export_logbook.attach_file_to_notion_page("page-123", "upload-123", "test.pdf", "token")
        self.assertTrue(ok)

    @patch("urllib.request.urlopen")
    def test_attach_file_to_notion_page_retries_without_status_on_status_failure(self, mock_urlopen):
        import io
        from urllib.error import HTTPError
        err_response = io.BytesIO(b'{"message": "status property invalid"}')
        err = HTTPError("url", 400, "Bad Request", {}, err_response)

        success_response = io.BytesIO(b'{"id": "page-123"}')
        success_response.status = 200

        # First call fails with HTTPError, second call (retry) succeeds
        mock_urlopen.side_effect = [err, unittest.mock.MagicMock(__enter__=unittest.mock.MagicMock(return_value=success_response))]

        ok = export_logbook.attach_file_to_notion_page("page-123", "upload-123", "test.pdf", "token", mark_done=True)
        self.assertTrue(ok)

    @patch("export_logbook.call_notion_api")
    def test_find_logbook_page_id(self, mock_api):
        mock_api.return_value = {"results": [{"id": "page-week-5"}]}
        page_id = export_logbook.find_logbook_page_id("logbook-db", 5, "token")
        self.assertEqual(page_id, "page-week-5")

        mock_api.return_value = {"results": []}
        page_id_none = export_logbook.find_logbook_page_id("logbook-db", 99, "token")
        self.assertIsNone(page_id_none)

    @patch("export_logbook.advance_next_week_status", return_value=True)
    @patch("export_logbook.find_logbook_page_id", return_value="page-week-5")
    @patch("export_logbook.create_notion_file_upload", return_value=("up-1", "https://upload.url"))
    @patch("export_logbook.send_notion_file_bytes", return_value=True)
    @patch("export_logbook.attach_file_to_notion_page", return_value=True)
    def test_upload_pdf_to_notion_logbook_success(self, mock_attach, mock_send, mock_create, mock_find, mock_advance):
        import tempfile
        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 test")
            tmp.flush()
            ok = export_logbook.upload_pdf_to_notion_logbook("logbook-db", 5, tmp.name, "token")
            self.assertTrue(ok)
            mock_find.assert_called_once_with("logbook-db", 5, "token")
            mock_create.assert_called_once()
            mock_send.assert_called_once()
            mock_attach.assert_called_once()
            mock_advance.assert_called_once_with("logbook-db", 5, "token")

    def test_upload_pdf_to_notion_logbook_missing_file_returns_false(self):
        ok = export_logbook.upload_pdf_to_notion_logbook("logbook-db", 5, "/non/existent/file.pdf", "token")
        self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value=None)
    def test_upload_pdf_to_notion_logbook_page_not_found_returns_false(self, mock_find):
        import tempfile
        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 test")
            tmp.flush()
            ok = export_logbook.upload_pdf_to_notion_logbook("logbook-db", 99, tmp.name, "token")
            self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value="page-week-6")
    @patch("urllib.request.urlopen")
    def test_advance_next_week_status_success(self, mock_urlopen, mock_find):
        import io
        fake_response = io.BytesIO(b'{"id": "page-week-6"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        ok = export_logbook.advance_next_week_status("logbook-db", 5, "token")
        self.assertTrue(ok)
        mock_find.assert_called_once_with("logbook-db", 6, "token")

    def test_advance_next_week_status_beyond_week_16_returns_false(self):
        ok = export_logbook.advance_next_week_status("logbook-db", 16, "token")
        self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value=None)
    def test_advance_next_week_status_page_not_found_returns_false(self, mock_find):
        ok = export_logbook.advance_next_week_status("logbook-db", 5, "token")
        self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value="page-week-6")
    @patch("urllib.request.urlopen")
    def test_advance_next_week_status_http_error_returns_false(self, mock_urlopen, mock_find):
        import io
        from urllib.error import HTTPError
        mock_urlopen.side_effect = HTTPError("url", 500, "Error", {}, io.BytesIO(b"{}"))
        ok = export_logbook.advance_next_week_status("logbook-db", 5, "token")
        self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value="page-week-5")
    @patch("export_logbook.create_notion_file_upload")
    def test_upload_pdf_to_notion_logbook_network_error_returns_false(self, mock_create, mock_find):
        import tempfile
        from urllib.error import URLError
        mock_create.side_effect = URLError("Connection timed out")
        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 test")
            tmp.flush()
            ok = export_logbook.upload_pdf_to_notion_logbook("logbook-db", 5, tmp.name, "token")
            self.assertFalse(ok)

    @patch("export_logbook.find_logbook_page_id", return_value="page-week-5")
    @patch("export_logbook.create_notion_file_upload")
    def test_upload_pdf_to_notion_logbook_json_decode_error_returns_false(self, mock_create, mock_find):
        import tempfile
        import json
        mock_create.side_effect = json.JSONDecodeError("Expecting value", "doc", 0)
        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 test")
            tmp.flush()
            ok = export_logbook.upload_pdf_to_notion_logbook("logbook-db", 5, tmp.name, "token")
            self.assertFalse(ok)

    @patch("urllib.request.urlopen")
    def test_create_notion_file_upload_passes_timeout(self, mock_urlopen):
        import io
        fake_response = io.BytesIO(b'{"id": "up-1", "upload_url": "https://upload.url"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        export_logbook.create_notion_file_upload("test.pdf", "token")
        self.assertEqual(mock_urlopen.call_args.kwargs.get("timeout"), 30)

    @patch("urllib.request.urlopen")
    def test_send_notion_file_bytes_passes_timeout(self, mock_urlopen):
        import tempfile
        import io
        fake_response = io.BytesIO(b'{"status": "ok"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        with tempfile.NamedTemporaryFile(suffix=".pdf") as tmp:
            tmp.write(b"%PDF-1.4 test")
            tmp.flush()
            export_logbook.send_notion_file_bytes("https://upload.url", tmp.name, "token")
            self.assertEqual(mock_urlopen.call_args.kwargs.get("timeout"), 30)

    @patch("urllib.request.urlopen")
    def test_attach_file_to_notion_page_passes_timeout(self, mock_urlopen):
        import io
        fake_response = io.BytesIO(b'{"id": "p-1"}')
        fake_response.status = 200
        mock_urlopen.return_value.__enter__.return_value = fake_response

        export_logbook.attach_file_to_notion_page("p-1", "up-1", "test.pdf", "token")
        self.assertEqual(mock_urlopen.call_args.kwargs.get("timeout"), 30)


class TestCliLogbookUpload(unittest.TestCase):
    @patch.dict(os.environ, DEFAULT_CLI_ENV, clear=True)
    def test_cli_upload_notion_missing_db_returns_1(self):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "1", "--upload-notion"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 1)

    @patch.dict(os.environ, {
        **DEFAULT_CLI_ENV,
        "NOTION_LOGBOOK_DB_ID": "logbook-db-id",
    }, clear=True)
    @patch("export_logbook.fetch_sprint_by_number", return_value={"id": "s1", "properties": {}})
    @patch("export_logbook.fetch_tasks_for_sprint", return_value=[])
    @patch("export_logbook.generate_logbook_files", return_value=("data.json", "main.typ"))
    @patch("export_logbook.compile_typst", return_value=True)
    @patch("export_logbook.upload_pdf_to_notion_logbook", return_value=True)
    def test_cli_upload_notion_triggers_upload(self, mock_upload, mock_compile, mock_gen, mock_tasks, mock_sprint):
        with patch("sys.argv", ["export_logbook.py", "--sprint", "1", "--upload-notion"]):
            exit_code = export_logbook.main()
            self.assertEqual(exit_code, 0)
            mock_upload.assert_called_once()






