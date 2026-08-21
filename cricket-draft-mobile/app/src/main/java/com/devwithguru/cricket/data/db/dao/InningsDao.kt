package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.devwithguru.cricket.data.db.entity.InningsEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface InningsDao {
    @Query("SELECT * FROM innings WHERE fixtureId = :fixtureId ORDER BY inningsNumber")
    fun getInningsByFixture(fixtureId: String): Flow<List<InningsEntity>>

    @Query("SELECT * FROM innings WHERE fixtureId = :fixtureId AND inningsNumber = :inningsNumber")
    suspend fun getInnings(fixtureId: String, inningsNumber: Int): InningsEntity?

    @Query("SELECT * FROM innings WHERE fixtureId = :fixtureId AND inningsNumber = :inningsNumber")
    fun observeInnings(fixtureId: String, inningsNumber: Int): Flow<InningsEntity?>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertInnings(innings: InningsEntity): Long

    @Update
    suspend fun updateInnings(innings: InningsEntity)

    @Query("DELETE FROM innings WHERE fixtureId = :fixtureId")
    suspend fun deleteAllByFixture(fixtureId: String)
}
